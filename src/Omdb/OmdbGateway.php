<?php

namespace App\Omdb;

use App\Entity\Movie;
use RuntimeException;
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

    public function getFirstMovieByTitle(string $movieTitle)
    {
        $apiResponse = $this->httpClient->request('GET', sprintf(
            'http://www.omdbapi.com/?apikey=%s&s=%s',
            $this->omdbApiKey,
            $movieTitle
        ));

        $results = $apiResponse->toArray()['Search'];
        if(count($results) === 0) {
            throw new RuntimeException('No results.');
        }

        return $results[0];
    }
}