<?php

namespace App\Services;

use Illuminate\Support\Facades\{
    Http,
    Log
};

class GoogleTranslateService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.google_translate.api_key');
        $this->baseUrl = config('services.google_translate.base_url');
    }

    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = null)
    {
        try {
            $response = Http::post($this->baseUrl . '?key=' . $this->apiKey, [
                'q' => $text,
                'target' => $targetLanguage,
                'source' => $sourceLanguage,
                'format' => 'text',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data']['translations'][0]['translatedText'];
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Translation API error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function translateMultiple(array $texts, string $targetLanguage, ?string $sourceLanguage = null): array
    {
        try {
            $response = Http::post($this->baseUrl . '?key=' . $this->apiKey, [
                'q' => $texts,
                'target' => $targetLanguage,
                'source' => $sourceLanguage,
                'format' => 'text',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $translations = collect($data['data']['translations'])
                    ->map(function ($translation) {
                        return $translation['translatedText'];
                    })
                    ->toArray();

                return [
                    'success' => true,
                    'translations' => $translations,
                ];
            }

            return [
                'success' => false,
                'error' => 'Batch translation failed: ' . $response->body(),
            ];

        } catch (\Exception $e) {
            Log::error('Batch translation API error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable',
            ];
        }
    }

    public function detectLanguage(string $text)
    {
        try {
            $response = Http::post('https://translation.googleapis.com/language/translate/v2/detect' . '?key=' . $this->apiKey, [
                'q' => $text,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $detection = $data['data']['detections'][0][0];

                return $detection['language'];
            }

            return 'error';

        } catch (\Exception $e) {
            Log::error('Language detection API error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable',
            ];
        }
    }

    public function getSupportedLanguages(string $targetLanguage = 'en'): array
    {
        try {
            $response = Http::get($this->baseUrl . '/languages?key=' . $this->apiKey, [
                'target' => $targetLanguage,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'languages' => $data['data']['languages'],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to fetch languages',
            ];

        } catch (\Exception $e) {
            Log::error('Languages API error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service unavailable',
            ];
        }
    }
}
