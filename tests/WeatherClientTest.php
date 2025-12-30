<?php

use PHPUnit\Framework\TestCase;

class WeatherClientTest extends TestCase
{
    /**
     * Test Case 1
     * API Key harus tersedia
     */
    public function testApiKeyIsAvailable()
    {
        $this->assertNotEmpty(
            getenv('API_KEY'),
            'API Key tidak ditemukan di environment'
        );
    }

    /**
     * Test Case 2
     * File konfigurasi harus ada
     */
    public function testConfigFileExists()
    {
        $this->assertFileExists(
            __DIR__ . '/../config/config.php',
            'File config/config.php tidak ditemukan'
        );
    }

    /**
     * Test Case 3
     * Fungsi http_request_get harus terdefinisi
     */
    public function testHttpRequestFunctionExists()
    {
        require_once __DIR__ . '/../config/config.php';

        $this->assertTrue(
            function_exists('http_request_get'),
            'Fungsi http_request_get() tidak tersedia'
        );
    }

    /**
     * Test Case 4
     * REST API dapat dipanggil
     */
    public function testApiRequestReturnsResponse()
    {
        $apiKey = getenv('API_KEY');
        if (!$apiKey) {
            $this->markTestSkipped('API Key tidak tersedia');
        }

        $url = "https://api.openweathermap.org/data/2.5/weather?q=Jakarta&appid=$apiKey";
        $response = http_request_get($url);

        $this->assertNotFalse(
            $response,
            'Request ke OpenWeather API gagal'
        );
    }

    /**
     * Test Case 5
     * Response API harus berupa JSON valid
     */
    public function testApiResponseIsValidJson()
    {
        $apiKey = getenv('API_KEY');
        if (!$apiKey) {
            $this->markTestSkipped('API Key tidak tersedia');
        }

        $url = "https://api.openweathermap.org/data/2.5/weather?q=Jakarta&appid=$apiKey";
        $response = http_request_get($url);

        $decoded = json_decode($response, true);

        $this->assertIsArray(
            $decoded,
            'Response API bukan JSON yang valid'
        );

        $this->assertArrayHasKey(
            'weather',
            $decoded,
            'Key weather tidak ditemukan dalam response'
        );
    }
}
