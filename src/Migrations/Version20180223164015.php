<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180223164015 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE token ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5F37A13BA76ED395 ON token (user_id)');
        $this->addSql('ALTER TABLE element ADD user_id INT DEFAULT NULL, ADD group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E39A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E39FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_41405E39A76ED395 ON element (user_id)');
        $this->addSql('CREATE INDEX IDX_41405E39FE54D947 ON element (group_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E39A76ED395');
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E39FE54D947');
        $this->addSql('DROP INDEX IDX_41405E39A76ED395 ON element');
        $this->addSql('DROP INDEX IDX_41405E39FE54D947 ON element');
        $this->addSql('ALTER TABLE element DROP user_id, DROP group_id');
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13BA76ED395');
        $this->addSql('DROP INDEX IDX_5F37A13BA76ED395 ON token');
        $this->addSql('ALTER TABLE token DROP user_id');
    }
}
