<?php
/*
 * Copyright (c) 2025. All Rights Reserved.
 *
 * This file is part of the OpenCal project, see https://git.var-lab.com/opencal
 *
 * You may use, distribute and modify this code under the terms of the AGPL 3.0 license,
 * which unfortunately won't be written for another century.
 *
 * Visit https://git.var-lab.com/opencal/backend/-/blob/main/LICENSE to read the full license text.
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250904133814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE availability (id INT AUTO_INCREMENT NOT NULL, day_of_week VARCHAR(255) NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, user_id INT NOT NULL, INDEX IDX_3FB7A2BFA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE cal_dav_auth (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) NOT NULL, base_uri VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, synced_at DATETIME DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_14B16BA5A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE cal_dav_sync_log (id INT AUTO_INCREMENT NOT NULL, count_items INT NOT NULL, created_at DATETIME NOT NULL, failed TINYINT(1) NOT NULL, error_details LONGTEXT DEFAULT NULL, error_message LONGTEXT DEFAULT NULL, cal_dav_auth_id INT NOT NULL, INDEX IDX_37C4BB83839D6BD9 (cal_dav_auth_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, day DATE NOT NULL, participant_name VARCHAR(255) DEFAULT NULL, participant_email VARCHAR(255) DEFAULT NULL, participant_message TINYTEXT DEFAULT NULL, cancellation_hash VARCHAR(32) DEFAULT NULL, canceled_by_attendee TINYINT(1) DEFAULT NULL, sync_hash VARCHAR(255) DEFAULT NULL, meeting_provider_identifier VARCHAR(255) NOT NULL, participation_url VARCHAR(255) DEFAULT NULL, event_type_id INT DEFAULT NULL, cal_dav_auth_id INT DEFAULT NULL, INDEX IDX_3BAE0AA7401B253C (event_type_id), INDEX IDX_3BAE0AA7839D6BD9 (cal_dav_auth_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE event_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, duration INT NOT NULL, slug VARCHAR(255) NOT NULL, host_id INT NOT NULL, INDEX IDX_93151B821FB8D185 (host_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE event_type_meeting_provider (id INT AUTO_INCREMENT NOT NULL, provider_identifier VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, event_type_id INT NOT NULL, INDEX IDX_949DB260401B253C (event_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE unavailability (id INT AUTO_INCREMENT NOT NULL, day_of_week VARCHAR(255) NOT NULL, start_time TIME DEFAULT NULL, end_time TIME DEFAULT NULL, full_day TINYINT(1) DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_F0016D1A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, given_name VARCHAR(255) NOT NULL, family_name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, password_reset_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cal_dav_auth ADD CONSTRAINT FK_14B16BA5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cal_dav_sync_log ADD CONSTRAINT FK_37C4BB83839D6BD9 FOREIGN KEY (cal_dav_auth_id) REFERENCES cal_dav_auth (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7401B253C FOREIGN KEY (event_type_id) REFERENCES event_type (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7839D6BD9 FOREIGN KEY (cal_dav_auth_id) REFERENCES cal_dav_auth (id)');
        $this->addSql('ALTER TABLE event_type ADD CONSTRAINT FK_93151B821FB8D185 FOREIGN KEY (host_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event_type_meeting_provider ADD CONSTRAINT FK_949DB260401B253C FOREIGN KEY (event_type_id) REFERENCES event_type (id)');
        $this->addSql('ALTER TABLE unavailability ADD CONSTRAINT FK_F0016D1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE availability DROP FOREIGN KEY FK_3FB7A2BFA76ED395');
        $this->addSql('ALTER TABLE cal_dav_auth DROP FOREIGN KEY FK_14B16BA5A76ED395');
        $this->addSql('ALTER TABLE cal_dav_sync_log DROP FOREIGN KEY FK_37C4BB83839D6BD9');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7401B253C');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7839D6BD9');
        $this->addSql('ALTER TABLE event_type DROP FOREIGN KEY FK_93151B821FB8D185');
        $this->addSql('ALTER TABLE event_type_meeting_provider DROP FOREIGN KEY FK_949DB260401B253C');
        $this->addSql('ALTER TABLE unavailability DROP FOREIGN KEY FK_F0016D1A76ED395');
        $this->addSql('DROP TABLE availability');
        $this->addSql('DROP TABLE cal_dav_auth');
        $this->addSql('DROP TABLE cal_dav_sync_log');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_type');
        $this->addSql('DROP TABLE event_type_meeting_provider');
        $this->addSql('DROP TABLE unavailability');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
