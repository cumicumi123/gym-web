"""FastAPI inference server for the GymSense workout recommender."""

from __future__ import annotations

import logging
from contextlib import asynccontextmanager
from typing import Literal

from fastapi import FastAPI, HTTPException, Request
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field

from predictor import GymPredictor

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger("gymsense.ml")


class PredictRequest(BaseModel):
    usia: int = Field(ge=14, le=80, description="Usia dalam tahun")
    berat: float = Field(ge=30, le=200, description="Berat badan dalam kg")
    tinggi: float = Field(ge=120, le=220, description="Tinggi badan dalam cm")
    pengalaman_bulan: int = Field(ge=0, le=600, description="Pengalaman gym dalam bulan")
    tujuan: Literal["cutting", "bulking", "strength"]

    gender: Literal["Male", "Female"] | None = None
    workout_type: Literal["Strength", "Cardio", "Yoga", "HIIT"] | None = None
    max_bpm: int | None = Field(default=None, ge=100, le=230)
    avg_bpm: int | None = Field(default=None, ge=80, le=210)
    resting_bpm: int | None = Field(default=None, ge=35, le=120)
    session_duration: float | None = Field(default=None, ge=0.25, le=4)
    calories_burned: float | None = Field(default=None, ge=100, le=3000)
    fat_percentage: float | None = Field(default=None, ge=3, le=60)
    water_intake: float | None = Field(default=None, ge=0.5, le=6)
    workout_frequency: int | None = Field(default=None, ge=0, le=7)


class HealthResponse(BaseModel):
    status: str
    model: str | None = None
    model_key: str | None = None
    best_f1: float | None = None
    artifacts: str | None = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    app.state.predictor = None
    app.state.load_error = None
    try:
        app.state.predictor = GymPredictor()
        logger.info("Model dimuat: %s", app.state.predictor.model_name)
    except Exception as error:  # pragma: no cover - startup safety
        app.state.load_error = str(error)
        logger.exception("Gagal memuat model: %s", error)
    yield


app = FastAPI(
    title="GymSense ML Service",
    version="1.0.0",
    description="Klasifikasi level/intensitas program latihan berbasis scikit-learn.",
    lifespan=lifespan,
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=False,
    allow_methods=["GET", "POST"],
    allow_headers=["*"],
)


def get_predictor(request: Request) -> GymPredictor:
    predictor = getattr(request.app.state, "predictor", None)
    if predictor is None:
        raise HTTPException(
            status_code=503,
            detail=(
                "Model belum dimuat. Jalankan ml-service/train.py lalu restart server. "
                f"Detail: {getattr(request.app.state, 'load_error', 'unknown')}"
            ),
        )
    return predictor


@app.get("/", tags=["info"])
def root():
    return {
        "service": "GymSense ML",
        "version": "1.0.0",
        "endpoints": ["/health", "/metrics", "/predict"],
        "docs": "/docs",
    }


@app.get("/health", response_model=HealthResponse, tags=["monitoring"])
def health(request: Request):
    predictor = getattr(request.app.state, "predictor", None)
    if predictor is None:
        return HealthResponse(status="error")
    best_f1 = predictor.metrics.get("best_model", {}).get("value")
    return HealthResponse(
        status="ok",
        model=predictor.model_name,
        model_key=predictor.model_key,
        best_f1=best_f1,
        artifacts=str(predictor.artifacts_dir),
    )


@app.get("/metrics", tags=["monitoring"])
def metrics(request: Request):
    predictor = get_predictor(request)
    if not predictor.metrics:
        raise HTTPException(status_code=503, detail="metrics.json belum tersedia.")
    return predictor.metrics


@app.post("/predict", tags=["inference"])
def predict(payload: PredictRequest, request: Request):
    predictor = get_predictor(request)
    try:
        return predictor.predict(payload.model_dump())
    except Exception as error:
        logger.exception("Prediksi gagal: %s", error)
        raise HTTPException(status_code=500, detail="Prediksi gagal diproses.") from error


if __name__ == "__main__":
    import uvicorn

    uvicorn.run("app:app", host="127.0.0.1", port=8001, reload=False)
