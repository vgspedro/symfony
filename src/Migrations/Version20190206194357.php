<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190206194357 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking ADD tourtype_id INT DEFAULT NULL, DROP tourtype, CHANGE status status ENUM(\'pending\', \'canceled\', \'confirmed\')');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE9F18CFA FOREIGN KEY (tourtype_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE9F18CFA ON booking (tourtype_id)');
        $this->addSql('ALTER TABLE category ADD booking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C13301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('CREATE INDEX IDX_64C19C13301C60 ON category (booking_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE9F18CFA');
        $this->addSql('DROP INDEX IDX_E00CEDDE9F18CFA ON booking');
        $this->addSql('ALTER TABLE booking ADD tourtype VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, DROP tourtype_id, CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C13301C60');
        $this->addSql('DROP INDEX IDX_64C19C13301C60 ON category');
        $this->addSql('ALTER TABLE category DROP booking_id');
    }
}
