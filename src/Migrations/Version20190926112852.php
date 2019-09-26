<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190926112852 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE extra_payment (id INT AUTO_INCREMENT NOT NULL, log LONGTEXT NOT NULL, posted_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stripe_payment_logs (id INT AUTO_INCREMENT NOT NULL, booking_id INT DEFAULT NULL, log LONGTEXT NOT NULL, INDEX IDX_1DE33EB83301C60 (booking_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stripe_payment_logs ADD CONSTRAINT FK_1DE33EB83301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE booking ADD stripe_payment_logs_id INT DEFAULT NULL, ADD payment_status ENUM(\'incomplete\', \'canceled\', \'cleared\', \'completed\', \'denied\', \'failed\', \'held\', \'paid\', \'placed\', \'processing\', \'refunded\', \'refused\', \'removed\', \'returned\', \'reversed\', \'unclaimed\', \'approved\', \'canceled by user\', \'pending\', \'succeeded\', \'partial_refund\', \'uncaptured\'), ADD deposit_amount INT(11) UNSIGNED COMMENT "(DC2Type:money)" NOT NULL COMMENT \'(DC2Type:money)\', CHANGE posted_at posted_at DATETIME NOT NULL, CHANGE status status ENUM(\'pending\', \'canceled\', \'confirmed\')');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDED5CAC23A FOREIGN KEY (stripe_payment_logs_id) REFERENCES stripe_payment_logs (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDED5CAC23A ON booking (stripe_payment_logs_id)');
        $this->addSql('ALTER TABLE category ADD deposit NUMERIC(2, 2) DEFAULT \'0.00\' NOT NULL');
        $this->addSql('ALTER TABLE client CHANGE password password LONGTEXT DEFAULT NULL, CHANGE username username LONGTEXT DEFAULT NULL, CHANGE address address LONGTEXT DEFAULT NULL, CHANGE telephone telephone LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE logs CHANGE status status ENUM(\'update\', \'create\', \'delete\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDED5CAC23A');
        $this->addSql('DROP TABLE extra_payment');
        $this->addSql('DROP TABLE stripe_payment_logs');
        $this->addSql('DROP INDEX IDX_E00CEDDED5CAC23A ON booking');
        $this->addSql('ALTER TABLE booking DROP stripe_payment_logs_id, DROP payment_status, DROP deposit_amount, CHANGE posted_at posted_at DATE NOT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE category DROP deposit');
        $this->addSql('ALTER TABLE client CHANGE password password VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE username username VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE address address VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE telephone telephone VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE logs CHANGE status status VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
