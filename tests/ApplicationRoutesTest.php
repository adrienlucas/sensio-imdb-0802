<?php

namespace App\Tests;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationRoutesTest extends WebTestCase
{
    private static ?KernelBrowser $client;

    public function setUp(): void
    {
        self::ensureKernelShutdown();
        self::$client = static::createClient();
    }

    public function tearDown(): void
    {
        self::$client = null;
        parent::tearDown();
    }

    /**
     * @dataProvider provideApplicationRoutes
     */
    public function testApplicationRoutes(string $url, int $expectedStatusCode): void
    {
        self::$client->request(Request::METHOD_GET, $url);

        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }

    public function provideApplicationRoutes(): array
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $movie = $entityManager->getRepository(Movie::class)->findAll()[0];

        return [
            ['/', Response::HTTP_OK],
            ['/movie/'.$movie->getId(), Response::HTTP_OK],
            ['/toto', Response::HTTP_NOT_FOUND],
        ];
    }
}
