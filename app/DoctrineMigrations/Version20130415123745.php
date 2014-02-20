<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130415123745 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE Bookmark (id INT AUTO_INCREMENT NOT NULL, record_id INT DEFAULT NULL, user_id INT DEFAULT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_2314F04B4DFD750C (record_id), INDEX IDX_2314F04BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Comment (id INT AUTO_INCREMENT NOT NULL, record_id INT DEFAULT NULL, user_id INT DEFAULT NULL, text LONGTEXT NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_5BC96BF04DFD750C (record_id), INDEX IDX_5BC96BF0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Course (id INT AUTO_INCREMENT NOT NULL, faculty_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_11326A8F680CAB68 (faculty_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Faculty (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Metadata (id INT AUTO_INCREMENT NOT NULL, config_id INT DEFAULT NULL, record_id INT DEFAULT NULL, string_id INT DEFAULT NULL, text_id INT DEFAULT NULL, date_id INT DEFAULT NULL, time_id INT DEFAULT NULL, datetime_id INT DEFAULT NULL, number_id INT DEFAULT NULL, boolean_id INT DEFAULT NULL, option_id INT DEFAULT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_B662564224DB0683 (config_id), INDEX IDX_B66256424DFD750C (record_id), UNIQUE INDEX UNIQ_B66256424AC2F1F0 (string_id), UNIQUE INDEX UNIQ_B6625642698D3548 (text_id), UNIQUE INDEX UNIQ_B6625642B897366B (date_id), UNIQUE INDEX UNIQ_B66256425EEADD3B (time_id), UNIQUE INDEX UNIQ_B6625642436D055B (datetime_id), UNIQUE INDEX UNIQ_B662564230A1DE10 (number_id), UNIQUE INDEX UNIQ_B66256424E0D3ACE (boolean_id), INDEX IDX_B6625642A7C41D6F (option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE MetadataBoolean (id INT AUTO_INCREMENT NOT NULL, value TINYINT(1) NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE MetadataConfig (id INT AUTO_INCREMENT NOT NULL, definition_id VARCHAR(100) DEFAULT NULL, name VARCHAR(100) NOT NULL, displayOrder INT NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_7619066CD11EA911 (definition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE MetadataConfigDefinition (id VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE MetadataConfigOption (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, metadataConfig_id INT DEFAULT NULL, INDEX IDX_D03133F03B1ECABE (metadataConfig_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE MetadataDate (id INT AUTO_INCREMENT NOT NULL, value DATE NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE MetadataDateTime (id INT AUTO_INCREMENT NOT NULL, value DATETIME NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE MetadataNumber (id INT AUTO_INCREMENT NOT NULL, value NUMERIC(10, 0) NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE MetadataString (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(255) NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE MetadataText (id INT AUTO_INCREMENT NOT NULL, value LONGTEXT NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE MetadataTime (id INT AUTO_INCREMENT NOT NULL, value TIME NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Record (id INT AUTO_INCREMENT NOT NULL, course_id INT DEFAULT NULL, externalId VARCHAR(255) DEFAULT NULL, title VARCHAR(255) NOT NULL, recordDate DATE NOT NULL, visible TINYINT(1) NOT NULL, updated DATETIME DEFAULT NULL, created DATETIME NOT NULL, INDEX IDX_9C989AA7591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Record_Lecturers (record_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8BFAAD454DFD750C (record_id), INDEX IDX_8BFAAD45A76ED395 (user_id), PRIMARY KEY(record_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Record_Users (record_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4C28F0494DFD750C (record_id), INDEX IDX_4C28F049A76ED395 (user_id), PRIMARY KEY(record_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, commonName VARCHAR(255) NOT NULL, lastLogin DATETIME NOT NULL, UNIQUE INDEX UNIQ_2DA17977F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE View (id INT AUTO_INCREMENT NOT NULL, record_id INT DEFAULT NULL, viewCount INT NOT NULL, date DATE NOT NULL, INDEX IDX_5ECF04B04DFD750C (record_id), INDEX search_idx (record_id, date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE Bookmark ADD CONSTRAINT FK_2314F04B4DFD750C FOREIGN KEY (record_id) REFERENCES Record (id)");
        $this->addSql("ALTER TABLE Bookmark ADD CONSTRAINT FK_2314F04BA76ED395 FOREIGN KEY (user_id) REFERENCES User (id)");
        $this->addSql("ALTER TABLE Comment ADD CONSTRAINT FK_5BC96BF04DFD750C FOREIGN KEY (record_id) REFERENCES Record (id)");
        $this->addSql("ALTER TABLE Comment ADD CONSTRAINT FK_5BC96BF0A76ED395 FOREIGN KEY (user_id) REFERENCES User (id)");
        $this->addSql("ALTER TABLE Course ADD CONSTRAINT FK_11326A8F680CAB68 FOREIGN KEY (faculty_id) REFERENCES Faculty (id)");
        $this->addSql("ALTER TABLE Metadata ADD CONSTRAINT FK_B662564224DB0683 FOREIGN KEY (config_id) REFERENCES MetadataConfig (id)");
        $this->addSql("ALTER TABLE Metadata ADD CONSTRAINT FK_B66256424DFD750C FOREIGN KEY (record_id) REFERENCES Record (id)");
        $this->addSql("ALTER TABLE Metadata ADD CONSTRAINT FK_B66256424AC2F1F0 FOREIGN KEY (string_id) REFERENCES MetadataString (id)");
        $this->addSql("ALTER TABLE Metadata ADD CONSTRAINT FK_B6625642698D3548 FOREIGN KEY (text_id) REFERENCES MetadataText (id)");
        $this->addSql("ALTER TABLE Metadata ADD CONSTRAINT FK_B6625642B897366B FOREIGN KEY (date_id) REFERENCES MetadataDate (id)");
        $this->addSql("ALTER TABLE Metadata ADD CONSTRAINT FK_B66256425EEADD3B FOREIGN KEY (time_id) REFERENCES MetadataTime (id)");
        $this->addSql("ALTER TABLE Metadata ADD CONSTRAINT FK_B6625642436D055B FOREIGN KEY (datetime_id) REFERENCES MetadataDateTime (id)");
        $this->addSql("ALTER TABLE Metadata ADD CONSTRAINT FK_B662564230A1DE10 FOREIGN KEY (number_id) REFERENCES MetadataNumber (id)");
        $this->addSql("ALTER TABLE Metadata ADD CONSTRAINT FK_B66256424E0D3ACE FOREIGN KEY (boolean_id) REFERENCES MetadataBoolean (id)");
        $this->addSql("ALTER TABLE Metadata ADD CONSTRAINT FK_B6625642A7C41D6F FOREIGN KEY (option_id) REFERENCES MetadataConfigOption (id)");
        $this->addSql("ALTER TABLE MetadataConfig ADD CONSTRAINT FK_7619066CD11EA911 FOREIGN KEY (definition_id) REFERENCES MetadataConfigDefinition (id)");
        $this->addSql("ALTER TABLE MetadataConfigOption ADD CONSTRAINT FK_D03133F03B1ECABE FOREIGN KEY (metadataConfig_id) REFERENCES MetadataConfig (id)");
        $this->addSql("ALTER TABLE Record ADD CONSTRAINT FK_9C989AA7591CC992 FOREIGN KEY (course_id) REFERENCES Course (id)");
        $this->addSql("ALTER TABLE Record_Lecturers ADD CONSTRAINT FK_8BFAAD454DFD750C FOREIGN KEY (record_id) REFERENCES Record (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Record_Lecturers ADD CONSTRAINT FK_8BFAAD45A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Record_Users ADD CONSTRAINT FK_4C28F0494DFD750C FOREIGN KEY (record_id) REFERENCES Record (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Record_Users ADD CONSTRAINT FK_4C28F049A76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE View ADD CONSTRAINT FK_5ECF04B04DFD750C FOREIGN KEY (record_id) REFERENCES Record (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Record DROP FOREIGN KEY FK_9C989AA7591CC992");
        $this->addSql("ALTER TABLE Course DROP FOREIGN KEY FK_11326A8F680CAB68");
        $this->addSql("ALTER TABLE Metadata DROP FOREIGN KEY FK_B66256424E0D3ACE");
        $this->addSql("ALTER TABLE Metadata DROP FOREIGN KEY FK_B662564224DB0683");
        $this->addSql("ALTER TABLE MetadataConfigOption DROP FOREIGN KEY FK_D03133F03B1ECABE");
        $this->addSql("ALTER TABLE MetadataConfig DROP FOREIGN KEY FK_7619066CD11EA911");
        $this->addSql("ALTER TABLE Metadata DROP FOREIGN KEY FK_B6625642A7C41D6F");
        $this->addSql("ALTER TABLE Metadata DROP FOREIGN KEY FK_B6625642B897366B");
        $this->addSql("ALTER TABLE Metadata DROP FOREIGN KEY FK_B6625642436D055B");
        $this->addSql("ALTER TABLE Metadata DROP FOREIGN KEY FK_B662564230A1DE10");
        $this->addSql("ALTER TABLE Metadata DROP FOREIGN KEY FK_B66256424AC2F1F0");
        $this->addSql("ALTER TABLE Metadata DROP FOREIGN KEY FK_B6625642698D3548");
        $this->addSql("ALTER TABLE Metadata DROP FOREIGN KEY FK_B66256425EEADD3B");
        $this->addSql("ALTER TABLE Bookmark DROP FOREIGN KEY FK_2314F04B4DFD750C");
        $this->addSql("ALTER TABLE Comment DROP FOREIGN KEY FK_5BC96BF04DFD750C");
        $this->addSql("ALTER TABLE Metadata DROP FOREIGN KEY FK_B66256424DFD750C");
        $this->addSql("ALTER TABLE Record_Lecturers DROP FOREIGN KEY FK_8BFAAD454DFD750C");
        $this->addSql("ALTER TABLE Record_Users DROP FOREIGN KEY FK_4C28F0494DFD750C");
        $this->addSql("ALTER TABLE View DROP FOREIGN KEY FK_5ECF04B04DFD750C");
        $this->addSql("ALTER TABLE Bookmark DROP FOREIGN KEY FK_2314F04BA76ED395");
        $this->addSql("ALTER TABLE Comment DROP FOREIGN KEY FK_5BC96BF0A76ED395");
        $this->addSql("ALTER TABLE Record_Lecturers DROP FOREIGN KEY FK_8BFAAD45A76ED395");
        $this->addSql("ALTER TABLE Record_Users DROP FOREIGN KEY FK_4C28F049A76ED395");
        $this->addSql("DROP TABLE Bookmark");
        $this->addSql("DROP TABLE Comment");
        $this->addSql("DROP TABLE Course");
        $this->addSql("DROP TABLE Faculty");
        $this->addSql("DROP TABLE Metadata");
        $this->addSql("DROP TABLE MetadataBoolean");
        $this->addSql("DROP TABLE MetadataConfig");
        $this->addSql("DROP TABLE MetadataConfigDefinition");
        $this->addSql("DROP TABLE MetadataConfigOption");
        $this->addSql("DROP TABLE MetadataDate");
        $this->addSql("DROP TABLE MetadataDateTime");
        $this->addSql("DROP TABLE MetadataNumber");
        $this->addSql("DROP TABLE MetadataString");
        $this->addSql("DROP TABLE MetadataText");
        $this->addSql("DROP TABLE MetadataTime");
        $this->addSql("DROP TABLE Record");
        $this->addSql("DROP TABLE Record_Lecturers");
        $this->addSql("DROP TABLE Record_Users");
        $this->addSql("DROP TABLE User");
        $this->addSql("DROP TABLE View");
    }
}
