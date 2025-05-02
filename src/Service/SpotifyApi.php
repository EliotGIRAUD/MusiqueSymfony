<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpotifyApi
{
    private string $clientId;
    private string $clientSecret;
    private HttpClientInterface $httpClient;
    private ?string $accessToken = null;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->clientId = $_ENV['SPOTIFY_CLIENT_ID'];
        $this->clientSecret = $_ENV['SPOTIFY_CLIENT_SECRET'];
    }

    private function authenticate(): void
    {
        $response = $this->httpClient->request('POST', 'https://accounts.spotify.com/api/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            ],
            'body' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $data = $response->toArray();
        $this->accessToken = $data['access_token'];
    }

    public function fetchArtist(string $spotifyId): array
    {
        if (!$this->accessToken) {
            $this->authenticate();
        }

        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/artists/' . $spotifyId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
        ]);

        return $response->toArray();
    }
}
