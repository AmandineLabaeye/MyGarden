<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190417124720 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comments_publication (id INT AUTO_INCREMENT NOT NULL, users_id INT NOT NULL, publication_id INT NOT NULL, content VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, INDEX IDX_40ACEBF467B3B43D (users_id), INDEX IDX_40ACEBF438B217A7 (publication_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publications_profil (id INT AUTO_INCREMENT NOT NULL, users_id INT NOT NULL, publication VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL, INDEX IDX_B9B5F43167B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments_publication ADD CONSTRAINT FK_40ACEBF467B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comments_publication ADD CONSTRAINT FK_40ACEBF438B217A7 FOREIGN KEY (publication_id) REFERENCES publications_profil (id)');
        $this->addSql('ALTER TABLE publications_profil ADD CONSTRAINT FK_B9B5F43167B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comments_publication DROP FOREIGN KEY FK_40ACEBF438B217A7');
        $this->addSql('DROP TABLE comments_publication');
        $this->addSql('DROP TABLE publications_profil');
    }
}
