<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200515174847 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pet (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, is_alive TINYINT(1) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, gender VARCHAR(255) DEFAULT NULL, breed VARCHAR(255) DEFAULT NULL, about LONGTEXT DEFAULT NULL, is_looking_for_owner TINYINT(1) NOT NULL, date_of_birth DATE DEFAULT NULL, color VARCHAR(255) DEFAULT NULL, eye_color VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E4529B85989D9B62 (slug), INDEX IDX_E4529B85A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pet_media (pet_id VARCHAR(255) NOT NULL, media_id VARCHAR(255) NOT NULL, INDEX IDX_C75061AF966F7FB6 (pet_id), INDEX IDX_C75061AFEA9FDD75 (media_id), PRIMARY KEY(pet_id, media_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_token (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL, INDEX IDX_7BA2F5EBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, public_url VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, INDEX IDX_6A2CA10CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, first_name VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pet ADD CONSTRAINT FK_E4529B85A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pet_media ADD CONSTRAINT FK_C75061AF966F7FB6 FOREIGN KEY (pet_id) REFERENCES pet (id)');
        $this->addSql('ALTER TABLE pet_media ADD CONSTRAINT FK_C75061AFEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pet_media DROP FOREIGN KEY FK_C75061AF966F7FB6');
        $this->addSql('ALTER TABLE pet_media DROP FOREIGN KEY FK_C75061AFEA9FDD75');
        $this->addSql('ALTER TABLE pet DROP FOREIGN KEY FK_E4529B85A76ED395');
        $this->addSql('ALTER TABLE api_token DROP FOREIGN KEY FK_7BA2F5EBA76ED395');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CA76ED395');
        $this->addSql('DROP TABLE pet');
        $this->addSql('DROP TABLE pet_media');
        $this->addSql('DROP TABLE api_token');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE user');
    }
}
