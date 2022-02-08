<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationRoutesTest extends WebTestCase
{
    private static ?KernelBrowser $client;

    public function setUp(): void
    {
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
        return [
            ['/', Response::HTTP_OK],
            ['/movie', Response::HTTP_OK],
            ['/toto', Response::HTTP_NOT_FOUND],
        ];
    }
}
