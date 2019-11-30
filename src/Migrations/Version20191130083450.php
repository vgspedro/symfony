<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191130083450 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE faqs (id INT AUTO_INCREMENT NOT NULL, locales_id INT DEFAULT NULL, html LONGTEXT NOT NULL, name VARCHAR(50) NOT NULL, INDEX IDX_8934BEE5164006B8 (locales_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE faqs ADD CONSTRAINT FK_8934BEE5164006B8 FOREIGN KEY (locales_id) REFERENCES locales (id)');
        $this->addSql('ALTER TABLE booking CHANGE status status ENUM(\'pending\', \'canceled\', \'confirmed\'), CHANGE payment_status payment_status ENUM(\'incomplete\', \'canceled\', \'cleared\', \'completed\', \'denied\', \'failed\', \'held\', \'paid\', \'placed\', \'processing\', \'refunded\', \'refused\', \'removed\', \'returned\', \'reversed\', \'unclaimed\', \'approved\', \'canceled by user\', \'pending\', \'succeeded\', \'partial_refund\', \'uncaptured\')');
        $this->addSql('ALTER TABLE logs CHANGE status status ENUM(\'update\', \'create\', \'delete\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE faqs');
        $this->addSql('ALTER TABLE booking CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE payment_status payment_status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE logs CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
