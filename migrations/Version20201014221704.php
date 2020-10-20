<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201014221704 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work ADD CONSTRAINT FK_534E688066EF0CAB FOREIGN KEY (node_id) REFERENCES samsung (id)');
        $this->addSql('ALTER TABLE work ADD CONSTRAINT FK_534E68809749932E FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('CREATE INDEX IDX_534E688066EF0CAB ON work (node_id)');
        $this->addSql('CREATE INDEX IDX_534E68809749932E ON work (employee_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
