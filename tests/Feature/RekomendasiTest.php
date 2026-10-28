<?php

namespace Tests\Feature;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RekomendasiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config([
            'services.ml.url' => 'http://ml.test',
            'services.ml.timeout' => 2,
        ]);

        Cache::flush();
    }

    public function test_home_page_loads_with_metric_summary(): void
    {
        Http::fake([
            '*/metrics' => Http::response($this->metricsPayload(), 200),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('GymSense');
        $response->assertSee('Latihan yang tepat');
        $response->assertSee('Cara kerja');
        $response->assertSee('Panduan level');
        $response->assertSee('Pemula');
    }

    public function test_form_page_loads(): void
    {
        $response = $this->get(route('rekomendasi.create'));

        $response->assertOk();
        $response->assertSee('Mulai dari');
        $response->assertSee('Tujuan utama');
        $response->assertSee('Data latihan tambahan');
        $response->assertSee('name="workout_type"', false);
        $response->assertSee('Otomatis dari tujuan');
        $response->assertSee('HIIT');
        $response->assertSee('name="gender"', false);
    }

    public function test_form_requires_all_main_fields(): void
    {
        $response = $this->from(route('rekomendasi.create'))
            ->post(route('rekomendasi.store'), []);

        $response->assertRedirect(route('rekomendasi.create'));
        $response->assertSessionHasErrors([
            'usia',
            'tinggi',
            'berat',
            'pengalaman_bulan',
            'tujuan',
        ]);
        $response->assertSessionHasErrors(['usia' => 'usia wajib diisi.']);
    }

    public function test_form_rejects_values_outside_the_allowed_ranges(): void
    {
        $response = $this->from(route('rekomendasi.create'))
            ->post(route('rekomendasi.store'), [
                'usia' => 9,
                'tinggi' => 100,
                'berat' => 500,
                'pengalaman_bulan' => 999,
                'tujuan' => 'mobility',
            ]);

        $response->assertRedirect(route('rekomendasi.create'));
        $response->assertSessionHasErrors([
            'usia',
            'tinggi',
            'berat',
            'pengalaman_bulan',
            'tujuan',
        ]);
    }

    public function test_successful_prediction_renders_program_and_disclaimer(): void
    {
        Http::fake([
            '*/predict' => Http::response($this->predictionPayload(), 200),
        ]);

        $response = $this->post(route('rekomendasi.store'), [
            'usia' => 28,
            'berat' => 72,
            'tinggi' => 175,
            'pengalaman_bulan' => 18,
            'tujuan' => 'cutting',
            'workout_frequency' => 4,
        ]);

        $response->assertOk();
        $response->assertViewIs('rekomendasi.hasil');
        $response->assertSee('Menengah');
        $response->assertSee('Level kamu');
        $response->assertSee('Kecocokan program');
        $response->assertSee('Upper/Lower atau Push');
        $response->assertSee('Data yang dipakai');
        $response->assertSee('Bukan saran medis.');
        $response->assertSee('Perkiraan');

        Http::assertSent(function ($request) {
            return $request->url() === 'http://ml.test/predict'
                && $request['usia'] === 28
                && $request['tujuan'] === 'cutting'
                && $request['workout_frequency'] === 4;
        });
    }

    public function test_failed_ml_response_shows_friendly_error_without_500(): void
    {
        Http::fake([
            '*/predict' => Http::response(['detail' => 'model error'], 500),
        ]);

        $response = $this->from(route('rekomendasi.create'))
            ->post(route('rekomendasi.store'), $this->validPayload());

        $response->assertRedirect(route('rekomendasi.create'));
        $response->assertSessionHas('ml_error');
        $response->assertSessionHasInput('usia', 28);
        $this->assertStringContainsString(
            'Layanan rekomendasi sedang bermasalah',
            (string) session('ml_error'),
        );
    }

    public function test_ml_service_connection_error_is_handled_gracefully(): void
    {
        Http::fake(function () {
            throw new ConnectionException('Connection refused');
        });

        $response = $this->from(route('rekomendasi.create'))
            ->post(route('rekomendasi.store'), $this->validPayload());

        $response->assertRedirect(route('rekomendasi.create'));
        $response->assertSessionHas('ml_error');
        $this->assertStringContainsString(
            'tidak dapat dihubungi',
            (string) session('ml_error'),
        );
    }

    public function test_model_page_renders_comparison_and_heatmaps(): void
    {
        config(['app.show_ml_report' => true]);

        Http::fake([
            '*/metrics' => Http::response($this->metricsPayload(), 200),
        ]);

        $response = $this->get(route('model.index'));

        $response->assertOk();
        $response->assertSee('Ringkasan metrik');
        $response->assertSee('Random Forest');
        $response->assertSee('Logistic Regression');
        $response->assertSee('Support Vector Machine');
        $response->assertSee('Confusion matrix');
        $response->assertSee('Classification report');
        $response->assertSee('Per kelas, bukan hanya rata-rata');
        $response->assertSee('Catatan analisis');
        $response->assertSee('Metrik live dari FastAPI');
    }

    public function test_model_page_falls_back_to_local_artifacts_when_service_is_down(): void
    {
        config(['app.show_ml_report' => true]);

        Http::fake([
            '*/metrics' => Http::response([], 500),
        ]);

        $response = $this->get(route('model.index'));

        $response->assertOk();
        $response->assertSee('Dibaca dari artifacts lokal');
        $response->assertSee('Ringkasan metrik');
    }

    public function test_model_page_is_hidden_by_default(): void
    {
        config(['app.show_ml_report' => false]);

        $this->get(route('model.index'))->assertNotFound();
    }

    public function test_old_model_url_redirects_to_recommendation(): void
    {
        $response = $this->get('/model');

        $response->assertStatus(301);
        $response->assertRedirect(route('rekomendasi.create'));
    }

    public function test_about_page_explains_how_it_works(): void
    {
        Http::fake([
            '*/metrics' => Http::response($this->metricsPayload(), 200),
        ]);

        $response = $this->get(route('tentang'));

        $response->assertOk();
        $response->assertSee('Tentang GymSense');
        $response->assertSee('Cara kerja');
        $response->assertSee('Panduan level');
        $response->assertSee('bukan saran medis');
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'usia' => 28,
            'berat' => 72,
            'tinggi' => 175,
            'pengalaman_bulan' => 18,
            'tujuan' => 'cutting',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function predictionPayload(): array
    {
        return [
            'kategori' => 'Intermediate',
            'label' => 2,
            'probabilitas' => 0.51,
            'probabilities' => [
                'Beginner' => 0.05,
                'Intermediate' => 0.51,
                'Advanced' => 0.44,
            ],
            'model' => 'Random Forest',
            'model_key' => 'random_forest',
            'bmi' => 23.51,
            'features' => [],
            'fitur_model' => [
                ['label' => 'Usia', 'value' => 28, 'source' => 'input'],
                ['label' => 'Max BPM', 'value' => 192, 'source' => 'estimasi'],
                ['label' => 'BMI', 'value' => 23.51, 'source' => 'turunan'],
            ],
            'estimated' => ['Max BPM'],
            'adjusted' => [],
            'catatan' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function metricsPayload(): array
    {
        return [
            'version' => '1.0.0',
            'generated_at' => '2026-10-26T00:00:00+00:00',
            'dataset' => [
                'name' => 'Gym Members Exercise Tracking',
                'rows' => 973,
                'features' => 14,
                'classes' => ['Beginner', 'Intermediate', 'Advanced'],
                'class_distribution' => [
                    'Beginner' => 376,
                    'Intermediate' => 406,
                    'Advanced' => 191,
                ],
                'missing_values' => 0,
                'duplicate_rows' => 0,
            ],
            'preprocessing' => [
                'split' => [
                    'test_size' => 0.2,
                    'random_state' => 42,
                    'stratified' => true,
                ],
            ],
            'metric_average' => 'macro',
            'models' => [
                [
                    'key' => 'random_forest',
                    'name' => 'Random Forest',
                    'accuracy' => 0.9128,
                    'precision' => 0.9293,
                    'recall' => 0.9267,
                    'f1' => 0.9274,
                    'is_best' => true,
                    'provenance' => 'Model pengguna.',
                    'confusion_matrix' => [
                        [64, 11, 0],
                        [6, 76, 0],
                        [0, 0, 38],
                    ],
                    'per_class' => [
                        ['label' => 'Beginner', 'precision' => 0.9143, 'recall' => 0.8533, 'f1' => 0.8828, 'support' => 75],
                        ['label' => 'Intermediate', 'precision' => 0.8736, 'recall' => 0.9268, 'f1' => 0.8994, 'support' => 82],
                        ['label' => 'Advanced', 'precision' => 1.0, 'recall' => 1.0, 'f1' => 1.0, 'support' => 38],
                    ],
                ],
                [
                    'key' => 'logistic_regression',
                    'name' => 'Logistic Regression',
                    'accuracy' => 0.872,
                    'precision' => 0.894,
                    'recall' => 0.895,
                    'f1' => 0.894,
                    'is_best' => false,
                    'provenance' => 'Dilatih ulang.',
                    'confusion_matrix' => [
                        [60, 15, 0],
                        [8, 74, 0],
                        [0, 0, 38],
                    ],
                ],
                [
                    'key' => 'svm',
                    'name' => 'Support Vector Machine',
                    'accuracy' => 0.877,
                    'precision' => 0.898,
                    'recall' => 0.898,
                    'f1' => 0.898,
                    'is_best' => false,
                    'provenance' => 'Dilatih ulang.',
                    'confusion_matrix' => [
                        [61, 14, 0],
                        [7, 75, 0],
                        [0, 0, 38],
                    ],
                ],
            ],
            'best_model' => [
                'key' => 'random_forest',
                'name' => 'Random Forest',
                'value' => 0.9274,
                'file' => 'best_model.joblib',
            ],
            'analysis' => [
                'frequently_confused' => [
                    ['from' => 'Beginner', 'to' => 'Intermediate', 'count' => 11],
                ],
                'top_features' => [
                    ['feature' => 'Frekuensi latihan', 'importance' => 0.2],
                ],
                'notes' => ['Catatan analisis untuk pengujian.'],
            ],
        ];
    }
}
