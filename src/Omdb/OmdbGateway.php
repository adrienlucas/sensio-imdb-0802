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

    public function getFirstMovieByTitle(string $movieTitle): array
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

    public function getMoviesByTitle(string $movieTitle): array
    {
        $apiResponse = $this->httpClient->request('GET', sprintf(
            'http://www.omdbapi.com/?apikey=%s&s=%s',
            $this->omdbApiKey,
            $movieTitle
        ));
        $apiResponse = $apiResponse->toArray();

        if(isset($apiResponse['Error'])) {
            throw new RuntimeException($apiResponse['Error']);
        }

        if(!isset($apiResponse['Search'])) {
            throw new RuntimeException(sprintf('Invalid response (%s)', var_export($apiResponse, true)));
        }

        $results = $apiResponse['Search'];
        if(count($results) === 0) {
            throw new RuntimeException('No results.');
        }

        return $results;
    }
}