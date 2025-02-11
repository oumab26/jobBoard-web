<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210303165333 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offre_emploi MODIFY id_offre INT NOT NULL');
        $this->addSql('ALTER TABLE offre_emploi DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE offre_emploi CHANGE id_offre id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE offre_emploi ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offre_emploi MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE offre_emploi DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE offre_emploi CHANGE id id_offre INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE offre_emploi ADD PRIMARY KEY (id_offre)');
    }
}
