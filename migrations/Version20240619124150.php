<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240619124150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adjusts categories table and updates tasks table to include a foreign key to categories, ensuring column does not already exist.';
    }

    public function up(Schema $schema): void
    {
        // Checking and creating the categories table only if it does not exist
        $categoryTableExists = $this->connection->executeQuery("SHOW TABLES LIKE 'categories'")->rowCount() > 0;
        if (!$categoryTableExists) {
            $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', title VARCHAR(255) NOT NULL, slug VARCHAR(64) NOT NULL, UNIQUE INDEX uq_categories_title (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        // Checking and adding the category_id column to the tasks table only if it does not exist
        $columnExists = $this->connection->fetchOne("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tasks' AND COLUMN_NAME = 'category_id'");
        if ($columnExists == 0) {
            $this->addSql('ALTER TABLE tasks ADD category_id INT NOT NULL');
            $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_5058659712469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
            $this->addSql('CREATE INDEX IDX_5058659712469DE2 ON tasks (category_id)');
        }
    }

    public function down(Schema $schema): void
    {
        // Reverting changes
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_5058659712469DE2');
        $this->addSql('DROP INDEX IDX_5058659712469DE2 ON tasks');
        $this->addSql('ALTER TABLE tasks DROP category_id');
        $this->addSql('DROP TABLE categories');
    }
}


