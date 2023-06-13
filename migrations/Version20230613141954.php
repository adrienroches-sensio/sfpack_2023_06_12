<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613141954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Movie] Add a unique constraint on slug.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie AS SELECT id, slug, title, poster, released_at, plot FROM movie');
        $this->addSql('DROP TABLE movie');
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, poster VARCHAR(255) NOT NULL, released_at DATETIME NOT NULL --(DC2Type:datetimetz_immutable)
        , plot CLOB NOT NULL)');
        $this->addSql('INSERT INTO movie (id, slug, title, poster, released_at, plot) SELECT id, slug, title, poster, released_at, plot FROM __temp__movie');
        $this->addSql('DROP TABLE __temp__movie');
        $this->addSql('CREATE UNIQUE INDEX unique_movie_slug ON movie (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie AS SELECT id, slug, title, poster, released_at, plot FROM movie');
        $this->addSql('DROP TABLE movie');
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, poster VARCHAR(255) NOT NULL, released_at DATETIME NOT NULL --(DC2Type:datetimetz_immutable)
        , plot CLOB NOT NULL)');
        $this->addSql('INSERT INTO movie (id, slug, title, poster, released_at, plot) SELECT id, slug, title, poster, released_at, plot FROM __temp__movie');
        $this->addSql('DROP TABLE __temp__movie');
    }
}
