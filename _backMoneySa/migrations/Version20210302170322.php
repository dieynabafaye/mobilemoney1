<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210302170322 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_agence_transaction');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_agence_transaction (user_agence_id INT NOT NULL, transaction_id INT NOT NULL, INDEX IDX_C8DBC3BAD7C5BEE9 (user_agence_id), INDEX IDX_C8DBC3BA2FC0CB0F (transaction_id), PRIMARY KEY(user_agence_id, transaction_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_agence_transaction ADD CONSTRAINT FK_C8DBC3BA2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_agence_transaction ADD CONSTRAINT FK_C8DBC3BAD7C5BEE9 FOREIGN KEY (user_agence_id) REFERENCES user (id) ON DELETE CASCADE');
    }
}
