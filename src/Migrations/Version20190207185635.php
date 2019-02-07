<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190207185635 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE available (id INT AUTO_INCREMENT NOT NULL, booking_id INT DEFAULT NULL, tourtype_id INT DEFAULT NULL, date VARCHAR(10) NOT NULL, hour VARCHAR(6) NOT NULL, posted_at DATETIME NOT NULL, INDEX IDX_A58FA4853301C60 (booking_id), INDEX IDX_A58FA4859F18CFA (tourtype_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE available ADD CONSTRAINT FK_A58FA4853301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE available ADD CONSTRAINT FK_A58FA4859F18CFA FOREIGN KEY (tourtype_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE booking CHANGE status status ENUM(\'pending\', \'canceled\', \'confirmed\')');
        $this->addSql('ALTER TABLE category CHANGE warranty_payment_pt warranty_payment_pt VARCHAR(350) DEFAULT NULL, CHANGE warranty_payment_en warranty_payment_en VARCHAR(350) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE available');
        $this->addSql('ALTER TABLE booking CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE category CHANGE warranty_payment_pt warranty_payment_pt VARCHAR(350) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE warranty_payment_en warranty_payment_en VARCHAR(350) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
