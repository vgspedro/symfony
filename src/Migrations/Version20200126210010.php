<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200126210010 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE about_us CHANGE locales_id locales_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE about_us ADD CONSTRAINT FK_B52303C3164006B8 FOREIGN KEY (locales_id) REFERENCES locales (id)');
        $this->addSql('CREATE INDEX IDX_B52303C3164006B8 ON about_us (locales_id)');
        $this->addSql('ALTER TABLE available CHANGE category_id category_id INT DEFAULT NULL, CHANGE stock stock INT DEFAULT NULL, CHANGE lotation lotation INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE available ADD CONSTRAINT FK_A58FA48512469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_A58FA48512469DE2 ON available (category_id)');
        $this->addSql('ALTER TABLE blockdate CHANGE category_id category_id INT DEFAULT NULL, CHANGE blockdate blockdate VARCHAR(150) DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE blockdate ADD CONSTRAINT FK_857EE7BA12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_857EE7BA12469DE2 ON blockdate (category_id)');
        $this->addSql('ALTER TABLE booking CHANGE adult adult INT DEFAULT NULL, CHANGE children children INT DEFAULT NULL, CHANGE baby baby INT DEFAULT NULL, CHANGE status status ENUM(\'pending\', \'canceled\', \'confirmed\'), CHANGE available_id available_id INT DEFAULT NULL, CHANGE client_id client_id INT DEFAULT NULL, CHANGE payment_status payment_status ENUM(\'incomplete\', \'canceled\', \'cleared\', \'completed\', \'denied\', \'failed\', \'held\', \'paid\', \'placed\', \'processing\', \'refunded\', \'refused\', \'removed\', \'returned\', \'reversed\', \'unclaimed\', \'approved\', \'canceled by user\', \'pending\', \'succeeded\', \'partial_refund\', \'uncaptured\'), CHANGE stripe_payment_logs_id stripe_payment_logs_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE36D3FBA2 FOREIGN KEY (available_id) REFERENCES available (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDED5CAC23A FOREIGN KEY (stripe_payment_logs_id) REFERENCES stripe_payment_logs (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE19EB6921 ON booking (client_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE36D3FBA2 ON booking (available_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDED5CAC23A ON booking (stripe_payment_logs_id)');
        $this->addSql('ALTER TABLE category CHANGE warranty_payment_pt warranty_payment_pt VARCHAR(1000) DEFAULT NULL, CHANGE warranty_payment_en warranty_payment_en VARCHAR(1000) DEFAULT NULL, CHANGE duration duration VARCHAR(5) DEFAULT \'00:00\' NOT NULL, CHANGE order_by order_by INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE client CHANGE locale_id locale_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455E559DFD1 FOREIGN KEY (locale_id) REFERENCES locales (id)');
        $this->addSql('CREATE INDEX IDX_C7440455E559DFD1 ON client (locale_id)');
        $this->addSql('ALTER TABLE company CHANGE telephone telephone VARCHAR(20) DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL, CHANGE link_facebook link_facebook VARCHAR(255) DEFAULT NULL, CHANGE link_twitter link_twitter VARCHAR(255) DEFAULT NULL, CHANGE link_instagram link_instagram VARCHAR(255) DEFAULT NULL, CHANGE link_linken link_linken VARCHAR(255) DEFAULT NULL, CHANGE link_pinterest link_pinterest VARCHAR(255) DEFAULT NULL, CHANGE link_my_domain link_my_domain VARCHAR(255) DEFAULT NULL, CHANGE link_youtube link_youtube VARCHAR(255) DEFAULT NULL, CHANGE link_behance link_behance VARCHAR(255) DEFAULT NULL, CHANGE link_youtube_active link_youtube_active TINYINT(1) DEFAULT NULL, CHANGE link_behance_active link_behance_active TINYINT(1) DEFAULT NULL, CHANGE currency_id currency_id INT DEFAULT NULL, CHANGE country_id country_id INT DEFAULT NULL, CHANGE link_snapchat link_snapchat VARCHAR(255) DEFAULT NULL, CHANGE link_snapchat_active link_snapchat_active TINYINT(1) DEFAULT NULL, CHANGE stripe_pk stripe_pk VARCHAR(255) DEFAULT NULL, CHANGE stripe_sk stripe_sk VARCHAR(255) DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FF92F3E70 FOREIGN KEY (country_id) REFERENCES countries (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F38248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('CREATE INDEX IDX_4FBF094FF92F3E70 ON company (country_id)');
        $this->addSql('CREATE INDEX IDX_4FBF094F38248176 ON company (currency_id)');
        $this->addSql('ALTER TABLE countries ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE currency ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE easytext DROP easy_text, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE event CHANGE category_id category_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA712469DE2 ON event (category_id)');
        $this->addSql('ALTER TABLE extra_payment ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE faqs CHANGE locales_id locales_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE faqs ADD CONSTRAINT FK_8934BEE5164006B8 FOREIGN KEY (locales_id) REFERENCES locales (id)');
        $this->addSql('CREATE INDEX IDX_8934BEE5164006B8 ON faqs (locales_id)');
        $this->addSql('ALTER TABLE feedback CHANGE booking_id booking_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D22944583301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('CREATE INDEX IDX_D22944583301C60 ON feedback (booking_id)');
        $this->addSql('ALTER TABLE gallery ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE job ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE locales ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE logs CHANGE status status ENUM(\'update\', \'create\', \'delete\'), ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE rgpd CHANGE locales_id locales_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE rgpd ADD CONSTRAINT FK_C80AB619164006B8 FOREIGN KEY (locales_id) REFERENCES locales (id)');
        $this->addSql('CREATE INDEX IDX_C80AB619164006B8 ON rgpd (locales_id)');
        $this->addSql('ALTER TABLE staff CHANGE job_id job_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF392BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('CREATE INDEX IDX_426EF392BE04EA9 ON staff (job_id)');
        $this->addSql('ALTER TABLE stripe_payment_logs CHANGE booking_id booking_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE stripe_payment_logs ADD CONSTRAINT FK_1DE33EB83301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('CREATE INDEX IDX_1DE33EB83301C60 ON stripe_payment_logs (booking_id)');
        $this->addSql('ALTER TABLE terms_conditions CHANGE locales_id locales_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE terms_conditions ADD CONSTRAINT FK_7BF59952164006B8 FOREIGN KEY (locales_id) REFERENCES locales (id)');
        $this->addSql('CREATE INDEX IDX_7BF59952164006B8 ON terms_conditions (locales_id)');
        $this->addSql('ALTER TABLE user ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('ALTER TABLE warning ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE about_us MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE about_us DROP FOREIGN KEY FK_B52303C3164006B8');
        $this->addSql('DROP INDEX IDX_B52303C3164006B8 ON about_us');
        $this->addSql('ALTER TABLE about_us DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE about_us CHANGE locales_id locales_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE available MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE available DROP FOREIGN KEY FK_A58FA48512469DE2');
        $this->addSql('DROP INDEX IDX_A58FA48512469DE2 ON available');
        $this->addSql('ALTER TABLE available DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE available CHANGE category_id category_id INT DEFAULT NULL, CHANGE stock stock INT DEFAULT NULL, CHANGE lotation lotation INT DEFAULT NULL');
        $this->addSql('ALTER TABLE blockdate MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE blockdate DROP FOREIGN KEY FK_857EE7BA12469DE2');
        $this->addSql('DROP INDEX IDX_857EE7BA12469DE2 ON blockdate');
        $this->addSql('ALTER TABLE blockdate DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE blockdate CHANGE category_id category_id INT DEFAULT NULL, CHANGE blockdate blockdate VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE booking MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE19EB6921');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE36D3FBA2');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDED5CAC23A');
        $this->addSql('DROP INDEX IDX_E00CEDDE19EB6921 ON booking');
        $this->addSql('DROP INDEX IDX_E00CEDDE36D3FBA2 ON booking');
        $this->addSql('DROP INDEX IDX_E00CEDDED5CAC23A ON booking');
        $this->addSql('ALTER TABLE booking DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE booking CHANGE client_id client_id INT DEFAULT NULL, CHANGE available_id available_id INT DEFAULT NULL, CHANGE stripe_payment_logs_id stripe_payment_logs_id INT DEFAULT NULL, CHANGE adult adult INT DEFAULT NULL, CHANGE children children INT DEFAULT NULL, CHANGE baby baby INT DEFAULT NULL, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE payment_status payment_status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE category MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE category DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE category CHANGE warranty_payment_pt warranty_payment_pt VARCHAR(1000) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE warranty_payment_en warranty_payment_en VARCHAR(1000) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE duration duration VARCHAR(5) CHARACTER SET utf8mb4 DEFAULT \'\'\'00:00\'\'\' NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE order_by order_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455E559DFD1');
        $this->addSql('DROP INDEX IDX_C7440455E559DFD1 ON client');
        $this->addSql('ALTER TABLE client DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE client CHANGE locale_id locale_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE company MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FF92F3E70');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F38248176');
        $this->addSql('DROP INDEX IDX_4FBF094FF92F3E70 ON company');
        $this->addSql('DROP INDEX IDX_4FBF094F38248176 ON company');
        $this->addSql('ALTER TABLE company DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE company CHANGE country_id country_id INT DEFAULT NULL, CHANGE currency_id currency_id INT DEFAULT NULL, CHANGE telephone telephone VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_my_domain link_my_domain VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_facebook link_facebook VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_twitter link_twitter VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_instagram link_instagram VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_linken link_linken VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_pinterest link_pinterest VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_youtube link_youtube VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_behance link_behance VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_snapchat link_snapchat VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_youtube_active link_youtube_active TINYINT(1) DEFAULT \'NULL\', CHANGE link_behance_active link_behance_active TINYINT(1) DEFAULT \'NULL\', CHANGE link_snapchat_active link_snapchat_active TINYINT(1) DEFAULT \'NULL\', CHANGE stripe_pk stripe_pk VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE stripe_sk stripe_sk VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE countries MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE countries DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE currency MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE currency DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE easytext MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE easytext DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE easytext ADD easy_text LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE event MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA712469DE2');
        $this->addSql('DROP INDEX IDX_3BAE0AA712469DE2 ON event');
        $this->addSql('ALTER TABLE event DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE event CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE extra_payment MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE extra_payment DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE faqs MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE faqs DROP FOREIGN KEY FK_8934BEE5164006B8');
        $this->addSql('DROP INDEX IDX_8934BEE5164006B8 ON faqs');
        $this->addSql('ALTER TABLE faqs DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE faqs CHANGE locales_id locales_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D22944583301C60');
        $this->addSql('DROP INDEX IDX_D22944583301C60 ON feedback');
        $this->addSql('ALTER TABLE feedback DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE feedback CHANGE booking_id booking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gallery MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE gallery DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE job MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE job DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE locales MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE locales DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE logs MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE logs DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE logs CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE rgpd MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE rgpd DROP FOREIGN KEY FK_C80AB619164006B8');
        $this->addSql('DROP INDEX IDX_C80AB619164006B8 ON rgpd');
        $this->addSql('ALTER TABLE rgpd DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE rgpd CHANGE locales_id locales_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE staff MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE staff DROP FOREIGN KEY FK_426EF392BE04EA9');
        $this->addSql('DROP INDEX IDX_426EF392BE04EA9 ON staff');
        $this->addSql('ALTER TABLE staff DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE staff CHANGE job_id job_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stripe_payment_logs MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE stripe_payment_logs DROP FOREIGN KEY FK_1DE33EB83301C60');
        $this->addSql('DROP INDEX IDX_1DE33EB83301C60 ON stripe_payment_logs');
        $this->addSql('ALTER TABLE stripe_payment_logs DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE stripe_payment_logs CHANGE booking_id booking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE terms_conditions MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE terms_conditions DROP FOREIGN KEY FK_7BF59952164006B8');
        $this->addSql('DROP INDEX IDX_7BF59952164006B8 ON terms_conditions');
        $this->addSql('ALTER TABLE terms_conditions DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE terms_conditions CHANGE locales_id locales_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE warning MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE warning DROP PRIMARY KEY');
    }
}
