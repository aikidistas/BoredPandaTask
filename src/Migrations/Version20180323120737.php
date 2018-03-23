<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180323120737 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE channel (id VARCHAR(24) NOT NULL, title VARCHAR(100) NOT NULL, external_uploads_playlist_id VARCHAR(34) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, video_id VARCHAR(11) NOT NULL, text VARCHAR(100) NOT NULL, INDEX IDX_389B78329C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE versioned_like (id INT AUTO_INCREMENT NOT NULL, video_id VARCHAR(11) NOT NULL, amount INT NOT NULL, date_time DATETIME NOT NULL, INDEX IDX_69BDF91D29C1004E (video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id VARCHAR(11) NOT NULL, channel_id VARCHAR(24) NOT NULL, INDEX IDX_7CC7DA2C72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B78329C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE versioned_like ADD CONSTRAINT FK_69BDF91D29C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C72F5A1AA');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B78329C1004E');
        $this->addSql('ALTER TABLE versioned_like DROP FOREIGN KEY FK_69BDF91D29C1004E');
        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE versioned_like');
        $this->addSql('DROP TABLE video');
    }
}
