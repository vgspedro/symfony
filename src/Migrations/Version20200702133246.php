<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200702133246 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE about_us CHANGE locales_id locales_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE available CHANGE category_id category_id INT DEFAULT NULL, CHANGE stock stock INT DEFAULT NULL, CHANGE lotation lotation INT DEFAULT NULL');
        $this->addSql('ALTER TABLE blockdate CHANGE category_id category_id INT DEFAULT NULL, CHANGE blockdate blockdate VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE booking CHANGE available_id available_id INT DEFAULT NULL, CHANGE client_id client_id INT DEFAULT NULL, CHANGE stripe_payment_logs_id stripe_payment_logs_id INT DEFAULT NULL, CHANGE adult adult INT DEFAULT NULL, CHANGE children children INT DEFAULT NULL, CHANGE baby baby INT DEFAULT NULL, CHANGE status status ENUM(\'pending\', \'canceled\', \'confirmed\'), CHANGE payment_status payment_status ENUM(\'incomplete\', \'canceled\', \'cleared\', \'completed\', \'denied\', \'failed\', \'held\', \'paid\', \'placed\', \'processing\', \'refunded\', \'refused\', \'removed\', \'returned\', \'reversed\', \'unclaimed\', \'approved\', \'canceled by user\', \'pending\', \'succeeded\', \'partial_refund\', \'uncaptured\')');
        $this->addSql('ALTER TABLE category ADD small_description_pt VARCHAR(50) NOT NULL, ADD small_description_en VARCHAR(50) NOT NULL, CHANGE warranty_payment_pt warranty_payment_pt VARCHAR(1000) DEFAULT NULL, CHANGE warranty_payment_en warranty_payment_en VARCHAR(1000) DEFAULT NULL, CHANGE duration duration VARCHAR(5) DEFAULT \'00:00\' NOT NULL, CHANGE order_by order_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client CHANGE locale_id locale_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE company CHANGE currency_id currency_id INT DEFAULT NULL, CHANGE country_id country_id INT DEFAULT NULL, CHANGE telephone telephone VARCHAR(20) DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL, CHANGE link_facebook link_facebook VARCHAR(255) DEFAULT NULL, CHANGE link_twitter link_twitter VARCHAR(255) DEFAULT NULL, CHANGE link_instagram link_instagram VARCHAR(255) DEFAULT NULL, CHANGE link_linken link_linken VARCHAR(255) DEFAULT NULL, CHANGE link_pinterest link_pinterest VARCHAR(255) DEFAULT NULL, CHANGE link_my_domain link_my_domain VARCHAR(255) DEFAULT NULL, CHANGE link_youtube link_youtube VARCHAR(255) DEFAULT NULL, CHANGE link_behance link_behance VARCHAR(255) DEFAULT NULL, CHANGE link_youtube_active link_youtube_active TINYINT(1) DEFAULT NULL, CHANGE link_behance_active link_behance_active TINYINT(1) DEFAULT NULL, CHANGE link_snapchat link_snapchat VARCHAR(255) DEFAULT NULL, CHANGE link_snapchat_active link_snapchat_active TINYINT(1) DEFAULT NULL, CHANGE stripe_pk stripe_pk VARCHAR(255) DEFAULT NULL, CHANGE stripe_sk stripe_sk VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE event CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE faqs CHANGE locales_id locales_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback CHANGE booking_id booking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE logs CHANGE status status ENUM(\'update\', \'create\', \'delete\')');
        $this->addSql('ALTER TABLE rgpd CHANGE locales_id locales_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE staff CHANGE job_id job_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stripe_payment_logs CHANGE booking_id booking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE terms_conditions CHANGE locales_id locales_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE about_us CHANGE locales_id locales_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE available CHANGE category_id category_id INT DEFAULT NULL, CHANGE stock stock INT DEFAULT NULL, CHANGE lotation lotation INT DEFAULT NULL');
        $this->addSql('ALTER TABLE blockdate CHANGE category_id category_id INT DEFAULT NULL, CHANGE blockdate blockdate VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE booking CHANGE client_id client_id INT DEFAULT NULL, CHANGE available_id available_id INT DEFAULT NULL, CHANGE stripe_payment_logs_id stripe_payment_logs_id INT DEFAULT NULL, CHANGE adult adult INT DEFAULT NULL, CHANGE children children INT DEFAULT NULL, CHANGE baby baby INT DEFAULT NULL, CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE payment_status payment_status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE category DROP small_description_pt, DROP small_description_en, CHANGE warranty_payment_pt warranty_payment_pt VARCHAR(1000) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE warranty_payment_en warranty_payment_en VARCHAR(1000) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE duration duration VARCHAR(5) CHARACTER SET utf8mb4 DEFAULT \'\'\'00:00\'\'\' NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE order_by order_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client CHANGE locale_id locale_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE company CHANGE country_id country_id INT DEFAULT NULL, CHANGE currency_id currency_id INT DEFAULT NULL, CHANGE telephone telephone VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_my_domain link_my_domain VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_facebook link_facebook VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_twitter link_twitter VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_instagram link_instagram VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_linken link_linken VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_pinterest link_pinterest VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_youtube link_youtube VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_behance link_behance VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_snapchat link_snapchat VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE link_youtube_active link_youtube_active TINYINT(1) DEFAULT \'NULL\', CHANGE link_behance_active link_behance_active TINYINT(1) DEFAULT \'NULL\', CHANGE link_snapchat_active link_snapchat_active TINYINT(1) DEFAULT \'NULL\', CHANGE stripe_pk stripe_pk VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE stripe_sk stripe_sk VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE event CHANGE category_id category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE faqs CHANGE locales_id locales_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback CHANGE booking_id booking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE logs CHANGE status status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE rgpd CHANGE locales_id locales_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE staff CHANGE job_id job_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stripe_payment_logs CHANGE booking_id booking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE terms_conditions CHANGE locales_id locales_id INT DEFAULT NULL');
    }
}
