<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180603140028 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE12469DE2');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE71F7E88B');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEC2B4C295');
        $this->addSql('DROP INDEX IDX_E00CEDDE12469DE2 ON booking');
        $this->addSql('DROP INDEX IDX_E00CEDDEC2B4C295 ON booking');
        $this->addSql('DROP INDEX IDX_E00CEDDE71F7E88B ON booking');
        $this->addSql('ALTER TABLE booking DROP category_id, DROP block_date_id, DROP event_id, DROP name, DROP email, DROP address');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE booking ADD category_id INT DEFAULT NULL, ADD block_date_id INT DEFAULT NULL, ADD event_id INT DEFAULT NULL, ADD name VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, ADD email VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, ADD address VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEC2B4C295 FOREIGN KEY (block_date_id) REFERENCES blockdate (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE12469DE2 ON booking (category_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDEC2B4C295 ON booking (block_date_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE71F7E88B ON booking (event_id)');
    }
}
