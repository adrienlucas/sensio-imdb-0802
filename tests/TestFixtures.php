<?php

namespace App\Tests;

use App\Entity\Genre;
use App\Entity\Movie;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class TestFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        // Create first genre
        $genre = new Genre();
        $genre->setName('comedy');

        $manager->persist($genre);

        // Create the first movie
        $movie = new Movie();
        $movie->setTitle('Don\'t look up');
        $movie->setDirector('Adam McKay');
        $movie->setYear(2021);
        $movie->setDescription('Two low-level astronomers must go on a giant media tour to warn mankind of an approaching comet that will destroy planet Earth.');

        $genre->addMovie($movie);
        $manager->persist($movie);
        $this->setReference('dummy_movie', $movie);

        // Create the second movie
        $movie = new Movie();
        $movie->setTitle('The Blues Brothers');
        $movie->setDirector('John Landis');
        $movie->setYear(1980);
        $movie->setDescription('Jake Blues rejoins with his brother Elwood after being released from prison, but the duo has just days to reunite their old R&B band and save the Catholic home where the two were raised, outrunning the police as they tear through Chicago.');

        $genre->addMovie($movie);
        $manager->persist($movie);

        // Create a movie without Genre
        $movie = new Movie();
        $movie->setTitle('A Clockwork Orange');
        $movie->setDirector('Stanley Kubrick');
        $movie->setYear(1971);
        $movie->setDescription('In the future, a sadistic gang leader is imprisoned and volunteers for a conduct-aversion experiment, but it doesn\'t go as planned.');

        $manager->persist($movie);

        $manager->flush();
    }
}