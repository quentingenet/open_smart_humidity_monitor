<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250209120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create sensor and humidity_measurement tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sensor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) DEFAULT NULL, humidity_threshold NUMERIC(5, 2) NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, api_key VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_BC8617B0C912ED9D (api_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE humidity_measurement (id INT AUTO_INCREMENT NOT NULL, sensor_id INT NOT NULL, humidity NUMERIC(5, 2) NOT NULL, measured_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9E3B3B6AA247991F (sensor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE humidity_measurement ADD CONSTRAINT FK_9E3B3B6AA247991F FOREIGN KEY (sensor_id) REFERENCES sensor (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE humidity_measurement DROP FOREIGN KEY FK_9E3B3B6AA247991F');
        $this->addSql('DROP TABLE sensor');
        $this->addSql('DROP TABLE humidity_measurement');
    }
}
