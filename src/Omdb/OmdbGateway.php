<?php

namespace App\Omdb;

use App\Entity\Movie;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbGateway
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $omdbApiKey,
    ) {
    }

    public function getImdbId(Movie $movie): ?string
    {
        $apiResponse = $this->httpClient->request('GET', sprintf(
            'http://www.omdbapi.com/?apikey=%s&t=%s',
            $this->omdbApiKey,
            $movie->getTitle()
        ));
        $json = $apiResponse->toArray();

        return $json['imdbID'] ?? null;
    }
}