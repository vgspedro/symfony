<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190207200228 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE available DROP FOREIGN KEY FK_A58FA4859F18CFA');
        $this->addSql('DROP INDEX IDX_A58FA4859F18CFA ON available');
        $this->addSql('ALTER TABLE available ADD datetime DATETIME NOT NULL, ADD stock INT DEFAULT NULL, DROP date, DROP hour, CHANGE tourtype_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE available ADD CONSTRAINT FK_A58FA48512469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_A58FA48512469DE2 ON available (category_id)');
        $this->addSql('ALTER TABLE booking CHANGE status status ENUM(\'pending\', \'canceled\', \'confirmed\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE available DROP FOREIGN KEY FK_A58FA48512469DE2');
        $this->addSql('DROP INDEX IDX_A58FA48512469DE2 ON available');
        $this->addSql('ALTER TABLE available ADD tourtype_id INT DEFAULT NULL, ADD date VARCHAR(10) NOT NULL COLLATE utf8mb4_unicode_ci, ADD hour VARCHAR(6) NOT NULL COLLATE utf8mb4_unicode_ci, DROP category_id, DROP datetime, DROP stock');
        $this->addSql('ALTER TABLE available ADD CONSTRAINT FK_A58FA4859F18CFA FOREIGN KEY (tourtype_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_A58FA4859F18CFA ON available (tourtype_id)');
        $this->addSql('ALTER TABLE booking CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
