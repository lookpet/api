<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200606120848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE breeder (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD breeder_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64933C95BB1 FOREIGN KEY (breeder_id) REFERENCES breeder (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64933C95BB1 ON user (breeder_id)');
        $this->addSql('ALTER TABLE pet ADD breeder_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pet ADD CONSTRAINT FK_E4529B8533C95BB1 FOREIGN KEY (breeder_id) REFERENCES breeder (id)');
        $this->addSql('CREATE INDEX IDX_E4529B8533C95BB1 ON pet (breeder_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64933C95BB1');
        $this->addSql('ALTER TABLE pet DROP FOREIGN KEY FK_E4529B8533C95BB1');
        $this->addSql('DROP TABLE breeder');
        $this->addSql('DROP INDEX IDX_E4529B8533C95BB1 ON pet');
        $this->addSql('ALTER TABLE pet DROP breeder_id');
        $this->addSql('DROP INDEX IDX_8D93D64933C95BB1 ON user');
        $this->addSql('ALTER TABLE user DROP breeder_id');
    }
}
