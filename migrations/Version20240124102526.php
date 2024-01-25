<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240124102526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product CHANGE type_id type_id INT DEFAULT NULL, CHANGE material_id material_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE308AC6F FOREIGN KEY (material_id) REFERENCES material (id)');
        $this->addSql('DROP INDEX material_id ON product');
        $this->addSql('CREATE INDEX IDX_D34A04ADE308AC6F ON product (material_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE308AC6F');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE308AC6F');
        $this->addSql('ALTER TABLE product CHANGE material_id material_id INT NOT NULL, CHANGE type_id type_id INT NOT NULL');
        $this->addSql('DROP INDEX idx_d34a04ade308ac6f ON product');
        $this->addSql('CREATE INDEX material_id ON product (material_id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE308AC6F FOREIGN KEY (material_id) REFERENCES material (id)');
    }
}
