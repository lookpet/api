<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201115221818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'User follower and user chat';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE user_follower (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, follower_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_595BED46A76ED395 (user_id), INDEX IDX_595BED46AC24F853 (follower_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_message (id VARCHAR(255) NOT NULL, from_user_id VARCHAR(255) NOT NULL, to_user_id VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_EEB02E752130303A (from_user_id), INDEX IDX_EEB02E7529F6EE60 (to_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_follower ADD CONSTRAINT FK_595BED46A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_follower ADD CONSTRAINT FK_595BED46AC24F853 FOREIGN KEY (follower_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E752130303A FOREIGN KEY (from_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E7529F6EE60 FOREIGN KEY (to_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE user_follower');
        $this->addSql('DROP TABLE user_message');
    }
}
