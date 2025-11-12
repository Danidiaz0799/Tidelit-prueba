<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Book and Review tables with their relationships';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
            title VARCHAR(255) NOT NULL, 
            author VARCHAR(255) NOT NULL, 
            published_year INTEGER NOT NULL
        )');
        
        $this->addSql('CREATE TABLE review (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
            book_id INTEGER NOT NULL, 
            rating INTEGER NOT NULL, 
            comment TEXT NOT NULL, 
            created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_794381C616A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        
        $this->addSql('CREATE INDEX IDX_794381C616A2B381 ON review (book_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE book');
    }
}
