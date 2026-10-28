"""CLI fallback: echo a JSON payload into predictor and print the JSON result.

Usage:
    python predict_cli.py '{"usia": 28, "berat": 70, "tinggi": 175, ...}'
    echo '{"usia": 28, ...}' | python predict_cli.py
"""

from __future__ import annotations

import json
import sys

from predictor import GymPredictor


def main() -> int:
    raw = sys.argv[1] if len(sys.argv) > 1 else sys.stdin.read()
    if not raw.strip():
        print(json.dumps({"error": "Payload JSON kosong."}), file=sys.stderr)
        return 1
    try:
        payload = json.loads(raw)
        result = GymPredictor().predict(payload)
    except Exception as error:
        print(json.dumps({"error": str(error)}, ensure_ascii=False), file=sys.stderr)
        return 1

    print(json.dumps(result, ensure_ascii=False))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
