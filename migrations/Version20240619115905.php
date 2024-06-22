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
        // this up() migration is auto-generated, please modify it to your needs
        $tableName = $schema->getTable('categories')->getName();
        $dbName = $this->connection->getDatabase();

        // Check if the 'slug' column already exists in the 'categories' table
        $columnExists = $this->connection->fetchOne("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = 'slug'", [$dbName, $tableName]);

        if ($columnExists == 0) {
            $this->addSql('ALTER TABLE categories ADD slug VARCHAR(64) NOT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        // Check if the 'slug' column exists before trying to drop it
        $tableName = $schema->getTable('categories')->getName();
        $dbName = $this->connection->getDatabase();

        // Check if the 'slug' column exists in the 'categories' table
        $columnExists = $this->connection->fetchOne("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = 'slug'", [$dbName, $tableName]);

        if ($columnExists > 0) {
            $this->addSql('ALTER TABLE categories DROP COLUMN slug');
        }
    }
}
