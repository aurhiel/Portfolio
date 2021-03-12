<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210306141800 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, quote_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, document_filename VARCHAR(255) DEFAULT NULL, sku VARCHAR(128) NOT NULL, date_paid DATE DEFAULT NULL, INDEX IDX_90651744DB805178 (quote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quote (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, label VARCHAR(255) NOT NULL, document_filename VARCHAR(255) DEFAULT NULL, sku VARCHAR(128) NOT NULL, date_created DATE NOT NULL, date_signed DATE DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, INDEX IDX_6B71CBF419EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF419EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744DB805178');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE quote');
    }
}
