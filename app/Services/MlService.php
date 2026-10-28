<?php

namespace App\Services;

use App\Exceptions\MlServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class MlService
{
    public function predict(array $payload): array
    {
        $url = $this->baseUrl().'/predict';

        try {
            $response = Http::timeout($this->timeout())
                ->acceptJson()
                ->asJson()
                ->post($url, $payload);
        } catch (ConnectionException $exception) {
            Log::warning('ML service tidak dapat dihubungi.', [
                'url' => $url,
                'message' => $exception->getMessage(),
            ]);

            throw new MlServiceException(
                'Layanan rekomendasi tidak dapat dihubungi. Coba lagi sebentar lagi.',
                previous: $exception,
            );
        } catch (Throwable $exception) {
            Log::error('Permintaan prediksi gagal.', [
                'url' => $url,
                'message' => $exception->getMessage(),
            ]);

            throw new MlServiceException('Terjadi kesalahan saat menyiapkan rekomendasi.', previous: $exception);
        }

        if ($response->failed()) {
            Log::error('Layanan ML mengembalikan status gagal.', [
                'url' => $url,
                'status' => $response->status(),
                'body' => Str::limit($response->body(), 500),
            ]);

            throw new MlServiceException('Layanan rekomendasi sedang bermasalah. Coba lagi beberapa saat lagi.');
        }

        $result = $response->json();

        if (! is_array($result) || ! isset($result['kategori'], $result['probabilitas'])) {
            Log::error('Respons layanan ML tidak memiliki struktur yang diharapkan.', [
                'body' => Str::limit($response->body(), 500),
            ]);

            throw new MlServiceException('Respons rekomendasi tidak valid. Coba lagi sebentar lagi.');
        }

        return $result;
    }

    /**
     * @return array{source: string, data: array}
     */
    public function metrics(): array
    {
        return Cache::remember('ml.metrics', 3600, function (): array {
            $live = $this->fetchMetricsFromService();

            if ($live !== null) {
                return ['source' => 'live', 'data' => $live];
            }

            $local = $this->readLocalMetrics();

            if ($local !== null) {
                return ['source' => 'local', 'data' => $local];
            }

            return ['source' => 'unavailable', 'data' => []];
        });
    }

    public function forgetMetrics(): void
    {
        Cache::forget('ml.metrics');
    }

    public function isUp(): bool
    {
        try {
            return Http::timeout(2)
                ->acceptJson()
                ->get($this->baseUrl().'/health')
                ->successful();
        } catch (Throwable) {
            return false;
        }
    }

    private function fetchMetricsFromService(): ?array
    {
        try {
            $response = Http::timeout($this->timeout())
                ->acceptJson()
                ->get($this->baseUrl().'/metrics');

            if ($response->successful() && is_array($response->json())) {
                return $response->json();
            }

            Log::debug('Metrik live tidak tersedia, memakai artefak lokal.', [
                'status' => $response->status(),
            ]);
        } catch (Throwable $exception) {
            Log::debug('Gagal mengambil metrik live.', ['message' => $exception->getMessage()]);
        }

        return null;
    }

    private function readLocalMetrics(): ?array
    {
        $path = base_path('ml-service/artifacts/metrics.json');

        if (! is_file($path)) {
            return null;
        }

        $contents = file_get_contents($path);
        $decoded = json_decode($contents ?: '', true);

        return is_array($decoded) ? $decoded : null;
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.ml.url'), '/');
    }

    private function timeout(): int
    {
        return (int) config('services.ml.timeout', 10);
    }
}
