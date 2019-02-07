<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190207204215 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE9F18CFA');
        $this->addSql('DROP INDEX IDX_E00CEDDE9F18CFA ON booking');
        $this->addSql('ALTER TABLE booking ADD available_id INT DEFAULT NULL, CHANGE status status ENUM(\'pending\', \'canceled\', \'confirmed\'), CHANGE tourtype_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE36D3FBA2 FOREIGN KEY (available_id) REFERENCES available (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE12469DE2 ON booking (category_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE36D3FBA2 ON booking (available_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE12469DE2');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE36D3FBA2');
        $this->addSql('DROP INDEX IDX_E00CEDDE12469DE2 ON booking');
        $this->addSql('DROP INDEX IDX_E00CEDDE36D3FBA2 ON booking');
        $this->addSql('ALTER TABLE booking ADD tourtype_id INT DEFAULT NULL, DROP category_id, DROP available_id, CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE9F18CFA FOREIGN KEY (tourtype_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE9F18CFA ON booking (tourtype_id)');
    }
}
