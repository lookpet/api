<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200727090218 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE breeder (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media_user (media_id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_4ED4099AEA9FDD75 (media_id), INDEX IDX_4ED4099AA76ED395 (user_id), PRIMARY KEY(media_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_token (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_7BA2F5EBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id VARCHAR(255) NOT NULL, breeder_id VARCHAR(255) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, roles JSON NOT NULL, first_name VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, provider VARCHAR(255) DEFAULT NULL, provider_id VARCHAR(255) DEFAULT NULL, provider_last_response LONGTEXT DEFAULT NULL, place_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649989D9B62 (slug), INDEX IDX_8D93D64933C95BB1 (breeder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pet_comment (id VARCHAR(255) NOT NULL, pet_id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, comment LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8A3AD28A966F7FB6 (pet_id), INDEX IDX_8A3AD28AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pet_like (id VARCHAR(255) NOT NULL, pet_id VARCHAR(255) DEFAULT NULL, user_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B72BA5D9966F7FB6 (pet_id), INDEX IDX_B72BA5D9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pet (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) DEFAULT NULL, breeder_id VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, is_alive TINYINT(1) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, gender VARCHAR(255) DEFAULT NULL, breed VARCHAR(255) DEFAULT NULL, about LONGTEXT DEFAULT NULL, is_looking_for_owner TINYINT(1) NOT NULL, date_of_birth DATE DEFAULT NULL, color VARCHAR(255) DEFAULT NULL, eye_color VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, father_name VARCHAR(255) DEFAULT NULL, mother_name VARCHAR(255) DEFAULT NULL, place_id VARCHAR(255) DEFAULT NULL, price VARCHAR(255) DEFAULT NULL, is_free TINYINT(1) DEFAULT NULL, is_sold TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_E4529B85989D9B62 (slug), INDEX IDX_E4529B85A76ED395 (user_id), INDEX IDX_E4529B8533C95BB1 (breeder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pet_media (pet_id VARCHAR(255) NOT NULL, media_id VARCHAR(255) NOT NULL, INDEX IDX_C75061AF966F7FB6 (pet_id), INDEX IDX_C75061AFEA9FDD75 (media_id), PRIMARY KEY(pet_id, media_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id VARCHAR(255) NOT NULL, user_id VARCHAR(255) NOT NULL, public_url VARCHAR(255) NOT NULL, height VARCHAR(255) DEFAULT NULL, width VARCHAR(255) DEFAULT NULL, path VARCHAR(255) DEFAULT NULL, mime VARCHAR(255) NOT NULL, cloudinary_id VARCHAR(255) DEFAULT NULL, cloudinary_url VARCHAR(255) DEFAULT NULL, is_s3_saved TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6A2CA10CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media_user ADD CONSTRAINT FK_4ED4099AEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE media_user ADD CONSTRAINT FK_4ED4099AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64933C95BB1 FOREIGN KEY (breeder_id) REFERENCES breeder (id)');
        $this->addSql('ALTER TABLE pet_comment ADD CONSTRAINT FK_8A3AD28A966F7FB6 FOREIGN KEY (pet_id) REFERENCES pet (id)');
        $this->addSql('ALTER TABLE pet_comment ADD CONSTRAINT FK_8A3AD28AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pet_like ADD CONSTRAINT FK_B72BA5D9966F7FB6 FOREIGN KEY (pet_id) REFERENCES pet (id)');
        $this->addSql('ALTER TABLE pet_like ADD CONSTRAINT FK_B72BA5D9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pet ADD CONSTRAINT FK_E4529B85A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pet ADD CONSTRAINT FK_E4529B8533C95BB1 FOREIGN KEY (breeder_id) REFERENCES breeder (id)');
        $this->addSql('ALTER TABLE pet_media ADD CONSTRAINT FK_C75061AF966F7FB6 FOREIGN KEY (pet_id) REFERENCES pet (id)');
        $this->addSql('ALTER TABLE pet_media ADD CONSTRAINT FK_C75061AFEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64933C95BB1');
        $this->addSql('ALTER TABLE pet DROP FOREIGN KEY FK_E4529B8533C95BB1');
        $this->addSql('ALTER TABLE media_user DROP FOREIGN KEY FK_4ED4099AA76ED395');
        $this->addSql('ALTER TABLE api_token DROP FOREIGN KEY FK_7BA2F5EBA76ED395');
        $this->addSql('ALTER TABLE pet_comment DROP FOREIGN KEY FK_8A3AD28AA76ED395');
        $this->addSql('ALTER TABLE pet_like DROP FOREIGN KEY FK_B72BA5D9A76ED395');
        $this->addSql('ALTER TABLE pet DROP FOREIGN KEY FK_E4529B85A76ED395');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CA76ED395');
        $this->addSql('ALTER TABLE pet_comment DROP FOREIGN KEY FK_8A3AD28A966F7FB6');
        $this->addSql('ALTER TABLE pet_like DROP FOREIGN KEY FK_B72BA5D9966F7FB6');
        $this->addSql('ALTER TABLE pet_media DROP FOREIGN KEY FK_C75061AF966F7FB6');
        $this->addSql('ALTER TABLE media_user DROP FOREIGN KEY FK_4ED4099AEA9FDD75');
        $this->addSql('ALTER TABLE pet_media DROP FOREIGN KEY FK_C75061AFEA9FDD75');
        $this->addSql('DROP TABLE breeder');
        $this->addSql('DROP TABLE media_user');
        $this->addSql('DROP TABLE api_token');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE pet_comment');
        $this->addSql('DROP TABLE pet_like');
        $this->addSql('DROP TABLE pet');
        $this->addSql('DROP TABLE pet_media');
        $this->addSql('DROP TABLE media');
    }
}
