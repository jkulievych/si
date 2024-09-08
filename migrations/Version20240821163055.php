<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240821163055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE manyNews (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', slug VARCHAR(64) NOT NULL, comment LONGTEXT DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, rating_count INT DEFAULT NULL, INDEX IDX_599428B412469DE2 (category_id), INDEX IDX_599428B4F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manyNews_tags (news_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_233B3B09B5A459A0 (news_id), INDEX IDX_233B3B09BAD26311 (tag_id), PRIMARY KEY(news_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(64) NOT NULL, slug VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, autor_id INT DEFAULT NULL, INDEX IDX_527EDB2514D45BBE (autor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE manyNews ADD CONSTRAINT FK_599428B412469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE manyNews ADD CONSTRAINT FK_599428B4F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE manyNews_tags ADD CONSTRAINT FK_233B3B09B5A459A0 FOREIGN KEY (news_id) REFERENCES manyNews (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manyNews_tags ADD CONSTRAINT FK_233B3B09BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2514D45BBE FOREIGN KEY (autor_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_5058659712469DE2');
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY FK_50586597F675F31B');
        $this->addSql('DROP TABLE tasks');
        $this->addSql('DROP INDEX uq_categories_title ON categories');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tasks (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5058659712469DE2 (category_id), INDEX IDX_50586597F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_5058659712469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_50586597F675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE manyNews DROP FOREIGN KEY FK_599428B412469DE2');
        $this->addSql('ALTER TABLE manyNews DROP FOREIGN KEY FK_599428B4F675F31B');
        $this->addSql('ALTER TABLE manyNews_tags DROP FOREIGN KEY FK_233B3B09B5A459A0');
        $this->addSql('ALTER TABLE manyNews_tags DROP FOREIGN KEY FK_233B3B09BAD26311');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB2514D45BBE');
        $this->addSql('DROP TABLE manyNews');
        $this->addSql('DROP TABLE manyNews_tags');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE UNIQUE INDEX uq_categories_title ON categories (title)');
    }
}
