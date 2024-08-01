<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240619115905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds a slug column to the categories table if it does not already exist.';
    }

    public function up(Schema $schema): void
    {
        // Ensure the categories table exists
        if ($schema->hasTable('categories')) {
            $table = $schema->getTable('categories');

            // Check if the slug column already exists
            if (!$table->hasColumn('slug')) {
                $this->addSql('ALTER TABLE categories ADD slug VARCHAR(64) NOT NULL');
            }
        }
    }

    public function down(Schema $schema): void
    {
        // Ensure the categories table exists
        if ($schema->hasTable('categories')) {
            $table = $schema->getTable('categories');

            // Check if the slug column exists before trying to drop it
            if ($table->hasColumn('slug')) {
                $this->addSql('ALTER TABLE categories DROP COLUMN slug');
            }
        }
    }
}