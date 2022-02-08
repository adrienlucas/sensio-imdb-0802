<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    public function testDisplayTheMoviePage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/movie');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Hello MovieController!');
    }
}
