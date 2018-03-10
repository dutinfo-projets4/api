<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180310143646 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, last_update_ts DATETIME NOT NULL, INDEX IDX_6DC044C5A76ED395 (user_id), INDEX IDX_6DC044C5727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE challenge (id INT AUTO_INCREMENT NOT NULL, challenge VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE element (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, group_id INT DEFAULT NULL, content LONGTEXT NOT NULL, last_update_ts DATETIME NOT NULL, INDEX IDX_41405E39A76ED395 (user_id), INDEX IDX_41405E39FE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE token (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, machine_name VARCHAR(255) NOT NULL, ip VARCHAR(255) NOT NULL, login_ts DATETIME NOT NULL, last_update_ts DATETIME NOT NULL, INDEX IDX_5F37A13BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_admin TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5727ACA70 FOREIGN KEY (parent_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E39A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE element ADD CONSTRAINT FK_41405E39FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5727ACA70');
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E39FE54D947');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5A76ED395');
        $this->addSql('ALTER TABLE element DROP FOREIGN KEY FK_41405E39A76ED395');
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13BA76ED395');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE element');
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE user');
    }
}
