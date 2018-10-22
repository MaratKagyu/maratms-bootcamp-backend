<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181022175039 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, owner_app_id BIGINT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_date_time DATETIME NOT NULL, last_changed_date_time DATETIME NOT NULL, INDEX IDX_BDAFD8C8DC7D0D73 (owner_app_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_app (id BIGINT AUTO_INCREMENT NOT NULL, name LONGTEXT NOT NULL, type INT NOT NULL, create_date_time DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quote (id INT AUTO_INCREMENT NOT NULL, owner_app_id BIGINT DEFAULT NULL, author_id INT DEFAULT NULL, text LONGTEXT NOT NULL, created_date_time DATETIME NOT NULL, last_changed_date_time DATETIME NOT NULL, INDEX IDX_6B71CBF4DC7D0D73 (owner_app_id), INDEX IDX_6B71CBF4F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE author ADD CONSTRAINT FK_BDAFD8C8DC7D0D73 FOREIGN KEY (owner_app_id) REFERENCES client_app (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4DC7D0D73 FOREIGN KEY (owner_app_id) REFERENCES client_app (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4F675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4F675F31B');
        $this->addSql('ALTER TABLE author DROP FOREIGN KEY FK_BDAFD8C8DC7D0D73');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4DC7D0D73');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE client_app');
        $this->addSql('DROP TABLE quote');
    }
}
