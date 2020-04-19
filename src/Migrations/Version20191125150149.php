<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191125150149 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE state (id INT AUTO_INCREMENT NOT NULL, country_code CHAR(4) NOT NULL, name VARCHAR(255) NOT NULL COMMENT \'State name\', INDEX IDX_A393D2FBF026BB7C (country_code), INDEX state_idx (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'States list\' ');
        $this->addSql('CREATE TABLE taxes (code VARCHAR(20) NOT NULL COMMENT \'Tax code\', PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'Taxes list\' ');
        $this->addSql('CREATE TABLE tax_county (id INT AUTO_INCREMENT NOT NULL, tax_code VARCHAR(20) NOT NULL COMMENT \'Tax code\', county_id INT NOT NULL, amount INT UNSIGNED DEFAULT 0 NOT NULL COMMENT \'Tax amount in cent\', INDEX IDX_1438958E6B9A3F60 (tax_code), INDEX IDX_1438958E85E73F45 (county_id), INDEX tax_county_idx (tax_code, county_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'Taxes per county\' ');
        $this->addSql('CREATE TABLE tax_income (id INT AUTO_INCREMENT NOT NULL, tax_code VARCHAR(20) DEFAULT NULL COMMENT \'Tax code\', county_id INT NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX IDX_7372087B6B9A3F60 (tax_code), INDEX IDX_7372087B85E73F45 (county_id), INDEX tax_income_date_idx (date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE county (id INT AUTO_INCREMENT NOT NULL, state_id INT NOT NULL, name VARCHAR(255) NOT NULL COMMENT \'County name\', INDEX IDX_58E2FF255D83CC1 (state_id), INDEX county_idx (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'Counties list\' ');
        $this->addSql('CREATE TABLE country (code CHAR(4) NOT NULL, name VARCHAR(255) NOT NULL COMMENT \'Country name\', PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_general_ci` ENGINE = InnoDB COMMENT = \'Countries list\' ');
        $this->addSql('ALTER TABLE state ADD CONSTRAINT FK_A393D2FBF026BB7C FOREIGN KEY (country_code) REFERENCES country (code) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tax_county ADD CONSTRAINT FK_1438958E6B9A3F60 FOREIGN KEY (tax_code) REFERENCES taxes (code) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tax_county ADD CONSTRAINT FK_1438958E85E73F45 FOREIGN KEY (county_id) REFERENCES county (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tax_income ADD CONSTRAINT FK_7372087B6B9A3F60 FOREIGN KEY (tax_code) REFERENCES taxes (code) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tax_income ADD CONSTRAINT FK_7372087B85E73F45 FOREIGN KEY (county_id) REFERENCES county (id)');
        $this->addSql('ALTER TABLE county ADD CONSTRAINT FK_58E2FF255D83CC1 FOREIGN KEY (state_id) REFERENCES state (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE county DROP FOREIGN KEY FK_58E2FF255D83CC1');
        $this->addSql('ALTER TABLE tax_county DROP FOREIGN KEY FK_1438958E6B9A3F60');
        $this->addSql('ALTER TABLE tax_income DROP FOREIGN KEY FK_7372087B6B9A3F60');
        $this->addSql('ALTER TABLE tax_county DROP FOREIGN KEY FK_1438958E85E73F45');
        $this->addSql('ALTER TABLE tax_income DROP FOREIGN KEY FK_7372087B85E73F45');
        $this->addSql('ALTER TABLE state DROP FOREIGN KEY FK_A393D2FBF026BB7C');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP TABLE taxes');
        $this->addSql('DROP TABLE tax_county');
        $this->addSql('DROP TABLE tax_income');
        $this->addSql('DROP TABLE county');
        $this->addSql('DROP TABLE country');
    }
}
