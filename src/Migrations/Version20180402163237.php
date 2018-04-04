<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 * @codeCoverageIgnore
 */
class Version20180402163237 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE video_tag (video_id VARCHAR(11) NOT NULL, tag_id INT NOT NULL, INDEX IDX_F910728729C1004E (video_id), INDEX IDX_F9107287BAD26311 (tag_id), PRIMARY KEY(video_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE video_tag ADD CONSTRAINT FK_F910728729C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE video_tag ADD CONSTRAINT FK_F9107287BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B78329C1004E');
        $this->addSql('DROP INDEX IDX_389B78329C1004E ON tag');
        $this->addSql('ALTER TABLE tag DROP video_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE video_tag');
        $this->addSql('ALTER TABLE tag ADD video_id VARCHAR(11) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B78329C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('CREATE INDEX IDX_389B78329C1004E ON tag (video_id)');
    }
}
