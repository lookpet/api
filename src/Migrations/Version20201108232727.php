<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201108232727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add next_notification_after_date';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD next_notification_after_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP next_notification_after_date');
    }
}
