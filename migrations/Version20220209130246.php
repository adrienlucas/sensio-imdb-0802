<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220209130246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE movie_genre (movie_id INTEGER NOT NULL, genre_id INTEGER NOT NULL, PRIMARY KEY(movie_id, genre_id))');
        $this->addSql('CREATE INDEX IDX_FD1229648F93B6FC ON movie_genre (movie_id)');
        $this->addSql('CREATE INDEX IDX_FD1229644296D31F ON movie_genre (genre_id)');
        $this->addSql('DROP INDEX IDX_1D5EF26F4296D31F');
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie AS SELECT id, title, year, director, description FROM movie');
        $this->addSql('CREATE TABLE __temp__movie_genre AS SELECT id, genre_id FROM movie WHERE genre_id IS NOT NULL');
        $this->addSql('DROP TABLE movie');
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, year INTEGER DEFAULT NULL, director VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO movie (id, title, year, director, description) SELECT id, title, year, director, description FROM __temp__movie');
        $this->addSql('INSERT INTO movie_genre (movie_id, genre_id) SELECT id, genre_id FROM __temp__movie_genre');
        $this->addSql('DROP TABLE __temp__movie');
        $this->addSql('DROP TABLE __temp__movie_genre');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie_genre AS SELECT movie_id, genre_id FROM movie_genre');
        $this->addSql('DROP TABLE movie_genre');
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie AS SELECT id, title, year, director, description FROM movie');
        $this->addSql('DROP TABLE movie');
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, year INTEGER DEFAULT NULL, director VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, genre_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO movie (id, title, year, director, description) SELECT id, title, year, director, description FROM __temp__movie');
        $this->addSql('UPDATE movie SET genre_id=(SELECT genre_id FROM __temp__movie_genre WHERE movie_id=movie.id)');
        $this->addSql('DROP TABLE __temp__movie');
        $this->addSql('DROP TABLE __temp__movie_genre');
        $this->addSql('CREATE INDEX IDX_1D5EF26F4296D31F ON movie (genre_id)');
    }
}
