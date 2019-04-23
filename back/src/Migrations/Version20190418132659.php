<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190418132659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE colllect_user_filesystem_credentials (user_id INT NOT NULL, filesystem_provider_name VARCHAR(20) NOT NULL, credentials LONGTEXT NOT NULL, PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE colllect_user_filesystem_credentials ADD CONSTRAINT FK_FC039ABFA76ED395 FOREIGN KEY (user_id) REFERENCES colllect_user (id)');
        $this->addSql('ALTER TABLE oauth2_access_token CHANGE user_identifier user_identifier VARCHAR(128) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE colllect_user_filesystem_credentials');
        $this->addSql('ALTER TABLE oauth2_access_token CHANGE user_identifier user_identifier VARCHAR(128) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
