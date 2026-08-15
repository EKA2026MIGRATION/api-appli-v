<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190715164418 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE address (address_id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(48) DEFAULT NULL COLLATE utf8mb4_general_ci, address VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, address2 VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, postal VARCHAR(10) DEFAULT NULL COLLATE utf8mb4_general_ci, town VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, country VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, latitude NUMERIC(10, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\' NOT NULL, suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(address_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog (blog_id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, content BLOB DEFAULT NULL, photo VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, author VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(blog_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (category_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE child (child_id INT UNSIGNED AUTO_INCREMENT NOT NULL, gender VARCHAR(2) DEFAULT NULL COLLATE utf8mb4_general_ci, firstname VARCHAR(64) NOT NULL COLLATE utf8mb4_general_ci, lastname VARCHAR(64) NOT NULL COLLATE utf8mb4_general_ci, phone VARCHAR(35) DEFAULT NULL COLLATE utf8mb4_general_ci, birthdate DATE DEFAULT NULL, medical BLOB DEFAULT NULL, photo VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, school_id SMALLINT DEFAULT NULL, france_resident TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\' NOT NULL, suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, family_id INT DEFAULT NULL, PRIMARY KEY(child_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE child_child_link (child_child_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, child_id INT UNSIGNED NOT NULL, sibling_id INT UNSIGNED NOT NULL, relation VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, INDEX child_child_link_child_FK_1 (sibling_id), INDEX child_child_link_child_FK (child_id), PRIMARY KEY(child_child_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE child_person_link (child_person_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, child_id INT UNSIGNED NOT NULL, person_id INT UNSIGNED NOT NULL, relation VARCHAR(64) NOT NULL COLLATE utf8mb4_general_ci, INDEX child_person_link_person_FK (person_id), INDEX child_person_link_child_FK (child_id), PRIMARY KEY(child_person_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE child_presence (child_presence_id INT UNSIGNED AUTO_INCREMENT NOT NULL, registration_id INT UNSIGNED DEFAULT NULL, child_id INT UNSIGNED DEFAULT NULL, person_id INT UNSIGNED DEFAULT NULL, location_id TINYINT(1) DEFAULT NULL, date DATE DEFAULT NULL, start TIME DEFAULT NULL, end TIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(child_presence_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE component (component_id INT UNSIGNED AUTO_INCREMENT NOT NULL, name_fr VARCHAR(128) NOT NULL COLLATE utf8mb4_general_ci, name_en VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, vat DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(component_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE driver_zone (driver_zone_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, staff_id SMALLINT UNSIGNED DEFAULT NULL, postal VARCHAR(10) DEFAULT NULL COLLATE utf8mb4_general_ci, priority TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, INDEX driver_zone_driver_FK (staff_id), PRIMARY KEY(driver_zone_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE family (family_id TINYINT(1) NOT NULL, name VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(family_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE food (food_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, description VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, kind VARCHAR(48) DEFAULT NULL COLLATE utf8mb4_general_ci, status VARCHAR(32) DEFAULT NULL COLLATE utf8mb4_general_ci, photo VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(food_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE group_activity (group_activity_id INT UNSIGNED AUTO_INCREMENT NOT NULL, date DATE DEFAULT NULL, name VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, age VARCHAR(8) DEFAULT NULL COLLATE utf8mb4_general_ci, locked TINYINT(1) DEFAULT NULL, start TIME DEFAULT NULL, end TIME DEFAULT NULL, lunch TINYINT(1) DEFAULT NULL, comment BLOB DEFAULT NULL, location_id TINYINT(1) DEFAULT NULL, area VARCHAR(32) DEFAULT NULL COLLATE utf8mb4_general_ci, sport_id TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(group_activity_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE group_activity_staff_link (group_activity_staff_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, group_activity_id INT UNSIGNED DEFAULT NULL, staff_id SMALLINT UNSIGNED DEFAULT NULL, INDEX group_activity_staff_link_staff_FK (staff_id), INDEX group_activity_staff_link_group_activity_FK (group_activity_id), PRIMARY KEY(group_activity_staff_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invoice (invoice_id INT UNSIGNED AUTO_INCREMENT NOT NULL, child_id INT UNSIGNED DEFAULT NULL, name_fr VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, name_en VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, description_fr BLOB DEFAULT NULL, description_en BLOB DEFAULT NULL, date DATETIME DEFAULT NULL, number VARCHAR(24) DEFAULT NULL COLLATE utf8mb4_general_ci, payment_method VARCHAR(16) DEFAULT NULL COLLATE utf8mb4_general_ci, price_ttc DOUBLE PRECISION DEFAULT NULL, prices BLOB DEFAULT NULL, address VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, postal VARCHAR(10) DEFAULT NULL COLLATE utf8mb4_general_ci, town VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(invoice_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invoice_component (invoice_component_id INT UNSIGNED AUTO_INCREMENT NOT NULL, invoice_product_id INT UNSIGNED DEFAULT NULL, name_fr VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, name_en VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, vat DOUBLE PRECISION DEFAULT NULL, price_ht DOUBLE PRECISION DEFAULT NULL, price_vat DOUBLE PRECISION DEFAULT NULL, price_ttc DOUBLE PRECISION DEFAULT NULL, quantity TINYINT(1) DEFAULT NULL, total_ht DOUBLE PRECISION DEFAULT NULL, total_vat DOUBLE PRECISION DEFAULT NULL, total_ttc DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(invoice_component_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invoice_product (invoice_product_id INT UNSIGNED AUTO_INCREMENT NOT NULL, invoice_id INT UNSIGNED DEFAULT NULL, name_fr VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, name_en VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, description_fr BLOB DEFAULT NULL, description_en BLOB DEFAULT NULL, price_ht DOUBLE PRECISION DEFAULT NULL, total_ht DOUBLE PRECISION DEFAULT NULL, total_ttc DOUBLE PRECISION DEFAULT NULL, price_ttc DOUBLE PRECISION DEFAULT NULL, quantity TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(invoice_product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE location (location_id TINYINT(1) NOT NULL, name VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, address VARCHAR(512) DEFAULT NULL COLLATE utf8mb4_general_ci, photo VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(location_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mail (mail_id TINYINT(1) NOT NULL, title VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, content BLOB DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(mail_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE meal (meal_id INT UNSIGNED AUTO_INCREMENT NOT NULL, date DATE DEFAULT NULL, child_id INT UNSIGNED DEFAULT NULL, person_id INT UNSIGNED DEFAULT NULL, free_name VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, INDEX meal_child_FK (child_id), PRIMARY KEY(meal_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE meal_food_link (meal_food_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, meal_id INT UNSIGNED DEFAULT NULL, food_id SMALLINT UNSIGNED DEFAULT NULL, INDEX meal_food_link_food_FK (food_id), INDEX meal_food_link_meal_FK (meal_id), PRIMARY KEY(meal_food_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE parameter (parameter_id TINYINT(1) NOT NULL, name VARCHAR(48) DEFAULT NULL COLLATE utf8mb4_general_ci, value VARCHAR(48) DEFAULT NULL COLLATE utf8mb4_general_ci, is_active TINYINT(1) DEFAULT NULL, PRIMARY KEY(parameter_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE person (person_id INT UNSIGNED AUTO_INCREMENT NOT NULL, firstname VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, lastname VARCHAR(64) NOT NULL COLLATE utf8mb4_general_ci, photo VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\' NOT NULL, suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, family_id INT DEFAULT NULL, PRIMARY KEY(person_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE person_address_link (person_address_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, person_id INT UNSIGNED NOT NULL, address_id INT UNSIGNED NOT NULL, INDEX person_address_link_person_FK (person_id), INDEX person_address_link_address_FK (address_id), PRIMARY KEY(person_address_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE person_person_link (person_person_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, person_id INT UNSIGNED NOT NULL, related_id INT UNSIGNED NOT NULL, relation VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, INDEX child_child_link_child_FK_1 (related_id), INDEX child_child_link_child_FK (person_id), PRIMARY KEY(person_person_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE person_phone_link (person_phone_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, person_id INT UNSIGNED NOT NULL, phone_id INT UNSIGNED NOT NULL, INDEX person_phone_link_person_FK (person_id), INDEX person_phone_link_phone_FK (phone_id), PRIMARY KEY(person_phone_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE phone (phone_id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(48) DEFAULT NULL COLLATE utf8mb4_general_ci, phone VARCHAR(35) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\' NOT NULL, suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(phone_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pickup (pickup_id INT AUTO_INCREMENT NOT NULL, registration_id INT UNSIGNED DEFAULT NULL, child_id INT UNSIGNED DEFAULT NULL, kind VARCHAR(8) DEFAULT NULL COLLATE utf8mb4_general_ci, ride_id INT UNSIGNED DEFAULT NULL, sort_order TINYINT(1) DEFAULT NULL, start DATETIME DEFAULT NULL, phone VARCHAR(35) DEFAULT NULL COLLATE utf8mb4_general_ci, postal VARCHAR(10) DEFAULT NULL COLLATE utf8mb4_general_ci, address VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, latitude NUMERIC(10, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, status VARCHAR(32) DEFAULT NULL COLLATE utf8mb4_general_ci, status_change DATETIME DEFAULT NULL, places TINYINT(1) DEFAULT NULL, comment BLOB DEFAULT NULL, validated VARCHAR(16) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(pickup_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pickup_activity (pickup_activity_id INT UNSIGNED AUTO_INCREMENT NOT NULL, registration_id INT UNSIGNED DEFAULT NULL, date DATE DEFAULT NULL, start TIME DEFAULT NULL, end TIME DEFAULT NULL, status VARCHAR(32) DEFAULT NULL COLLATE utf8mb4_general_ci, status_change DATETIME DEFAULT NULL, validated VARCHAR(16) DEFAULT NULL COLLATE utf8mb4_general_ci, child_id INT UNSIGNED DEFAULT NULL, sport_id TINYINT(1) DEFAULT NULL, location_id TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(pickup_activity_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pickup_activity_group_activity_link (pickup_activity_group_activity_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, pickup_activity_id INT UNSIGNED DEFAULT NULL, group_activity_id INT UNSIGNED DEFAULT NULL, INDEX pickup_activity_group_activity_link_pickup_activity_FK (pickup_activity_id), INDEX pickup_activity_group_activity_link_group_activity_FK (group_activity_id), PRIMARY KEY(pickup_activity_group_activity_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product (product_id INT UNSIGNED AUTO_INCREMENT NOT NULL, family_id TINYINT(1) DEFAULT NULL, season_id SMALLINT UNSIGNED DEFAULT NULL, name_fr VARCHAR(128) NOT NULL COLLATE utf8mb4_general_ci, name_en VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, description_fr BLOB DEFAULT NULL, description_en BLOB DEFAULT NULL, price_ttc DOUBLE PRECISION DEFAULT NULL, prices BLOB DEFAULT NULL, transport TINYINT(1) DEFAULT \'0\', lunch TINYINT(1) DEFAULT NULL, child_id INT UNSIGNED DEFAULT NULL, photo VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, mail_id TINYINT(1) DEFAULT NULL, is_location_selectable TINYINT(1) DEFAULT NULL, is_date_selectable TINYINT(1) DEFAULT NULL, is_hour_selectable TINYINT(1) DEFAULT NULL, is_sport_selectable TINYINT(1) DEFAULT NULL, visibility VARCHAR(32) DEFAULT NULL COLLATE utf8mb4_general_ci, hour_dropin TIME DEFAULT NULL, hour_dropoff TIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\' NOT NULL, suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, INDEX product_family_FK (family_id), INDEX product_season_FK (season_id), PRIMARY KEY(product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_cancelled_date (product_cancelled_date_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, date DATE DEFAULT NULL, category_id SMALLINT UNSIGNED DEFAULT NULL, product_id INT UNSIGNED DEFAULT NULL, message_fr VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, message_en VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(product_cancelled_date_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_category_link (product_category_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, product_id INT UNSIGNED DEFAULT NULL, category_id SMALLINT UNSIGNED DEFAULT NULL, INDEX product_category_link_product_FK (product_id), INDEX product_category_link_category_FK (category_id), PRIMARY KEY(product_category_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_component (product_component_id INT UNSIGNED AUTO_INCREMENT NOT NULL, product_id INT UNSIGNED NOT NULL, name_fr VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, name_en VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, vat DOUBLE PRECISION DEFAULT NULL, price_ht DOUBLE PRECISION DEFAULT NULL, price_vat DOUBLE PRECISION DEFAULT NULL, price_ttc DOUBLE PRECISION DEFAULT NULL, quantity TINYINT(1) DEFAULT NULL, total_ht DOUBLE PRECISION DEFAULT NULL, total_vat DOUBLE PRECISION DEFAULT NULL, total_ttc DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, INDEX product_component_link_product_FK (product_id), PRIMARY KEY(product_component_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_date_link (product_date_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, product_id INT UNSIGNED DEFAULT NULL, date DATE DEFAULT NULL, INDEX product_date_link_product_FK (product_id), PRIMARY KEY(product_date_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_hour_link (product_hour_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, product_id INT UNSIGNED DEFAULT NULL, start TIME DEFAULT NULL, end TIME DEFAULT NULL, INDEX product_hour_link_product_FK (product_id), PRIMARY KEY(product_hour_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_location_link (product_location_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, product_id INT UNSIGNED DEFAULT NULL, location_id TINYINT(1) DEFAULT NULL, INDEX product_location_link_location_FK (location_id), INDEX product_location_link_product_FK (product_id), PRIMARY KEY(product_location_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_sport_link (product_sport_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, product_id INT UNSIGNED DEFAULT NULL, sport_id TINYINT(1) DEFAULT NULL, INDEX product_sport_link_sport_FK (sport_id), INDEX product_sport_link_product_FK (product_id), PRIMARY KEY(product_sport_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registration (registration_id INT UNSIGNED AUTO_INCREMENT NOT NULL, registration DATETIME DEFAULT NULL, child_id INT UNSIGNED DEFAULT NULL, person_id INT UNSIGNED DEFAULT NULL, product_id INT UNSIGNED DEFAULT NULL, invoice_id INT UNSIGNED DEFAULT NULL, payed DOUBLE PRECISION DEFAULT NULL, status VARCHAR(16) DEFAULT NULL COLLATE utf8mb4_general_ci, preferences BLOB DEFAULT NULL, sessions BLOB DEFAULT NULL, location_id TINYINT(1) DEFAULT NULL, sport_id TINYINT(1) DEFAULT NULL, transaction_id INT UNSIGNED DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(registration_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE registration_sport_link (registration_sport_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, registration_id INT UNSIGNED DEFAULT NULL, sport_id TINYINT(1) DEFAULT NULL, INDEX product_sport_link_sport_FK (sport_id), INDEX product_sport_link_product_FK (registration_id), PRIMARY KEY(registration_sport_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ride (ride_id INT UNSIGNED AUTO_INCREMENT NOT NULL, locked TINYINT(1) DEFAULT \'0\', kind VARCHAR(8) DEFAULT NULL COLLATE utf8mb4_general_ci, date DATE DEFAULT NULL, linked_ride_id INT UNSIGNED DEFAULT NULL, name VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, places TINYINT(1) DEFAULT NULL, start TIME DEFAULT NULL, arrival TIME DEFAULT NULL, start_point VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, end_point VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, vehicle_id TINYINT(1) DEFAULT NULL, staff_id SMALLINT UNSIGNED DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\' NOT NULL, suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, INDEX transport_vehicle_FK (vehicle_id), PRIMARY KEY(ride_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE school (school_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(48) DEFAULT NULL COLLATE utf8mb4_general_ci, address VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_general_ci, postal VARCHAR(10) DEFAULT NULL COLLATE utf8mb4_general_ci, town VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, country VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, latitude NUMERIC(10, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, google_place_id VARCHAR(8) DEFAULT NULL COLLATE utf8mb4_general_ci, photo VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\' NOT NULL, suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(school_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE season (season_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, status VARCHAR(32) DEFAULT NULL COLLATE utf8mb4_general_ci, date_start DATE DEFAULT NULL, date_end DATE DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(season_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sessions (sess_id VARCHAR(128) NOT NULL COLLATE utf8_bin, sess_data BLOB NOT NULL, sess_time INT UNSIGNED NOT NULL, sess_lifetime INT NOT NULL, PRIMARY KEY(sess_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sport (sport_id TINYINT(1) NOT NULL, name VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, kind VARCHAR(32) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(sport_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE staff (staff_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, kind VARCHAR(16) DEFAULT NULL COLLATE utf8mb4_general_ci, person_id INT UNSIGNED DEFAULT NULL, max_children TINYINT(1) DEFAULT NULL, priority TINYINT(1) DEFAULT NULL, vehicle_id TINYINT(1) DEFAULT NULL, address_id INT UNSIGNED DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, INDEX driver_vehicle_FK (vehicle_id), UNIQUE INDEX staff_UN (person_id), INDEX driver_address_FK (address_id), PRIMARY KEY(staff_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE staff_presence (staff_presence_id INT UNSIGNED AUTO_INCREMENT NOT NULL, staff_id TINYINT(1) DEFAULT NULL, date DATE DEFAULT NULL, start TIME DEFAULT NULL, end TIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(staff_presence_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(250) NOT NULL COLLATE utf8_general_ci, moment VARCHAR(30) NOT NULL COLLATE utf8_general_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE task_staff (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, staff_id INT NOT NULL, date_task DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE television (television_id INT UNSIGNED AUTO_INCREMENT NOT NULL, start TIME DEFAULT NULL, end TIME DEFAULT NULL, module VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(television_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE transaction (transaction_id INT UNSIGNED AUTO_INCREMENT NOT NULL, date DATETIME DEFAULT NULL, internal_order VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, status VARCHAR(24) DEFAULT NULL COLLATE utf8mb4_general_ci, number VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_general_ci, amount DOUBLE PRECISION DEFAULT NULL, person_id INT UNSIGNED DEFAULT NULL, invoice_id INT UNSIGNED DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(transaction_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, allow_use TINYINT(1) DEFAULT \'0\', identifier VARCHAR(32) NOT NULL COLLATE utf8mb4_general_ci, email VARCHAR(128) NOT NULL COLLATE utf8mb4_general_ci, creation DATETIME DEFAULT NULL, enabled TINYINT(1) DEFAULT \'0\', salt VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci, password VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci, token VARCHAR(40) DEFAULT NULL COLLATE utf8mb4_general_ci, password_request DATETIME DEFAULT NULL, roles LONGTEXT DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, UNIQUE INDEX un_identifier (identifier), UNIQUE INDEX un_email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_archives (id INT UNSIGNED AUTO_INCREMENT NOT NULL, allow_use TINYINT(1) DEFAULT \'0\', identifier VARCHAR(32) NOT NULL COLLATE utf8mb4_general_ci, email VARCHAR(128) NOT NULL COLLATE utf8mb4_general_ci, creation DATETIME DEFAULT NULL, enabled TINYINT(1) DEFAULT \'0\', salt VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_general_ci, password VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci, token VARCHAR(40) DEFAULT NULL COLLATE utf8mb4_general_ci, password_request DATETIME DEFAULT NULL, roles LONGTEXT DEFAULT NULL COLLATE utf8mb4_general_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_person_link (user_person_link_id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, person_id INT UNSIGNED NOT NULL, INDEX user_person_link_user_FK (user_id), UNIQUE INDEX user_person_link_UN (user_id), INDEX user_person_link_person_FK (person_id), PRIMARY KEY(user_person_link_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE vehicle (vehicle_id TINYINT(1) NOT NULL, name VARCHAR(32) NOT NULL COLLATE utf8mb4_general_ci, matriculation VARCHAR(16) NOT NULL COLLATE utf8mb4_general_ci, combustible VARCHAR(16) DEFAULT NULL COLLATE utf8mb4_general_ci, places TINYINT(1) DEFAULT NULL, photo VARCHAR(256) DEFAULT NULL COLLATE utf8mb4_general_ci, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\' NOT NULL, suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, PRIMARY KEY(vehicle_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE week (week_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, season_id SMALLINT UNSIGNED DEFAULT NULL, kind VARCHAR(8) DEFAULT NULL COLLATE utf8mb4_general_ci, code VARCHAR(8) DEFAULT NULL COLLATE utf8mb4_general_ci, name VARCHAR(32) DEFAULT NULL COLLATE utf8mb4_general_ci, date_start DATE DEFAULT NULL, created_at DATETIME DEFAULT NULL, created_by INT UNSIGNED DEFAULT NULL, updated_at DATETIME DEFAULT NULL, updated_by INT UNSIGNED DEFAULT NULL, suppressed TINYINT(1) DEFAULT \'0\', suppressed_at DATETIME DEFAULT NULL, suppressed_by INT UNSIGNED DEFAULT NULL, INDEX week_season_FK (season_id), PRIMARY KEY(week_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE address');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE blog');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE category');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE child');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE child_child_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE child_person_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE child_presence');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE component');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE driver_zone');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE family');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE food');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE group_activity');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE group_activity_staff_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE invoice');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE invoice_component');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE invoice_product');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE location');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE mail');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE meal');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE meal_food_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE parameter');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE person');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE person_address_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE person_person_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE person_phone_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE phone');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE pickup');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE pickup_activity');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE pickup_activity_group_activity_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_cancelled_date');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_category_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_component');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_date_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_hour_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_location_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_sport_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE registration');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE registration_sport_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ride');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE school');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE season');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sessions');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sport');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE staff');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE staff_presence');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE task');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE task_staff');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE television');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE transaction');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_archives');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_person_link');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE vehicle');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE week');
    }
}
