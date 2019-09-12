<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190912194106 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE stripe_refund_logs');
        $this->addSql('ALTER TABLE booking ADD stripe_payment_logs_id INT DEFAULT NULL, CHANGE status status ENUM(\'pending\', \'canceled\', \'confirmed\'), CHANGE payment_status payment_status ENUM(\'incomplete\', \'canceled\', \'cleared\', \'completed\', \'denied\', \'failed\', \'held\', \'paid\', \'placed\', \'processing\', \'refunded\', \'refused\', \'removed\', \'returned\', \'reversed\', \'unclaimed\', \'approved\', \'canceled by user\', \'pending\', \'succeeded\', \'partial_refund\', \'uncaptured\')');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDED5CAC23A FOREIGN KEY (stripe_payment_logs_id) REFERENCES stripe_payment_logs (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDED5CAC23A ON booking (stripe_payment_logs_id)');
        $this->addSql('ALTER TABLE logs CHANGE status status ENUM(\'update\', \'create\', \'delete\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE stripe_refund_logs (id INT AUTO_INCREMENT NOT NULL, booking_id INT DEFAULT NULL, log LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_F1B2CC2A3301C60 (booking_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE stripe_refund_logs ADD CONSTRAINT FK_F1B2CC2A3301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDED5CAC23A');
        $this->addSql('DROP INDEX IDX_E00CEDDED5CAC23A ON booking');
        $this->addSql('ALTER TABLE booking DROP stripe_payment_logs_id, CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE payment_status payment_status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE logs CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
