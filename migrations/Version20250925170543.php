<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250925170543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE additional_event_field (id INT AUTO_INCREMENT NOT NULL, field_type VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, event_type_id INT NOT NULL, INDEX IDX_2A4EBEBA401B253C (event_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE additional_event_field_value (id INT AUTO_INCREMENT NOT NULL, value LONGTEXT NOT NULL, field_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_CDF8DEF6443707B0 (field_id), INDEX IDX_CDF8DEF671F7E88B (event_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE additional_event_field ADD CONSTRAINT FK_2A4EBEBA401B253C FOREIGN KEY (event_type_id) REFERENCES event_type (id)');
        $this->addSql('ALTER TABLE additional_event_field_value ADD CONSTRAINT FK_CDF8DEF6443707B0 FOREIGN KEY (field_id) REFERENCES additional_event_field (id)');
        $this->addSql('ALTER TABLE additional_event_field_value ADD CONSTRAINT FK_CDF8DEF671F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE additional_event_field DROP FOREIGN KEY FK_2A4EBEBA401B253C');
        $this->addSql('ALTER TABLE additional_event_field_value DROP FOREIGN KEY FK_CDF8DEF6443707B0');
        $this->addSql('ALTER TABLE additional_event_field_value DROP FOREIGN KEY FK_CDF8DEF671F7E88B');
        $this->addSql('DROP TABLE additional_event_field');
        $this->addSql('DROP TABLE additional_event_field_value');
    }
}
