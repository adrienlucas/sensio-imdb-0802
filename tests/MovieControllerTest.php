<?php

namespace App\Tests;

use App\Entity\Genre;
use App\Entity\Movie;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    private Movie $dummyMovie;

    protected function setUp(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $purger = new ORMPurger($entityManager);
        $executor = new ORMExecutor($entityManager, $purger);

        $executor->execute([new TestFixtures()]);

        $this->dummyMovie = $executor->getReferenceRepository()->getReference('dummy_movie');
        self::ensureKernelShutdown();
    }

    public function testDisplayTheMoviePage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/movie/'.$this->dummyMovie->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h5.card-title', strtoupper($this->dummyMovie->getTitle()));
        $this->assertSelectorTextContains('p.card-text', $this->dummyMovie->getDescription());
        $genreNames = array_map(fn(Genre $genre) => $genre->getName(), $this->dummyMovie->getGenres()->toArray());
        $this->assertSelectorTextContains('.genres', implode(', ', $genreNames));
    }

    public function testDisplayTheMovieList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/list-movies');

        $this->assertCount(3, $client->getCrawler()->filter('ul.movies > li'));
    }

    public function testDisplayTheMovieListByGenre(): void
    {
        $client = static::createClient();
        $client->request('GET', '/list-movies/comedy');

        $this->assertCount(2, $client->getCrawler()->filter('ul.movies > li'));
    }
}
