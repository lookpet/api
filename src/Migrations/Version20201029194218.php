<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201029194218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds user events';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE user_event (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, source VARCHAR(255) DEFAULT NULL, medium VARCHAR(255) DEFAULT NULL, campaign VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, context LONGTEXT DEFAULT NULL, INDEX IDX_D96CF1FFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_event ADD CONSTRAINT FK_D96CF1FFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE user_event');
    }
}
