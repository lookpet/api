<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201108000722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'pet is deleted nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE pet CHANGE is_deleted is_deleted TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE pet CHANGE is_deleted is_deleted TINYINT(1) NOT NULL');
    }
}
