<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210302165759 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agence (id INT AUTO_INCREMENT NOT NULL, compte_id INT DEFAULT NULL, nom_agence VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_64C19AA9F2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, cni VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_transaction (client_id INT NOT NULL, transaction_id INT NOT NULL, INDEX IDX_737C20EA19EB6921 (client_id), INDEX IDX_737C20EA2FC0CB0F (transaction_id), PRIMARY KEY(client_id, transaction_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commission (id INT AUTO_INCREMENT NOT NULL, etat INT NOT NULL, transfert_argent INT NOT NULL, operateur_depot INT NOT NULL, operateur_retrait INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compte (id INT AUTO_INCREMENT NOT NULL, admin_system_id INT DEFAULT NULL, num_compte VARCHAR(255) NOT NULL, solde VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_CFF6526039622A97 (admin_system_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE depot (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, compte_id INT DEFAULT NULL, created_at DATETIME NOT NULL, montant INT NOT NULL, INDEX IDX_47948BBCA76ED395 (user_id), INDEX IDX_47948BBCF2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tarif (id INT AUTO_INCREMENT NOT NULL, montant_min INT NOT NULL, montant_max INT NOT NULL, frais_envoie INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, compte_id INT DEFAULT NULL, user_depot_id INT DEFAULT NULL, user_retrait_id INT DEFAULT NULL, client_envoi_id INT DEFAULT NULL, client_retrait_id INT DEFAULT NULL, montant INT NOT NULL, date_depot DATE NOT NULL, date_retrait DATE DEFAULT NULL, date_annulation DATE DEFAULT NULL, ttc INT NOT NULL, frais_etat INT NOT NULL, frais_system INT NOT NULL, frais_envoie INT NOT NULL, frais_retrait INT NOT NULL, code_transaction INT NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_723705D1F2C56620 (compte_id), INDEX IDX_723705D1659D30DE (user_depot_id), INDEX IDX_723705D1D99F8396 (user_retrait_id), INDEX IDX_723705D11171DC20 (client_envoi_id), INDEX IDX_723705D1EEAC783B (client_retrait_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, profil_id INT DEFAULT NULL, agence_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, cni VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, avatar LONGBLOB NOT NULL, type_id INT NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), INDEX IDX_8D93D649275ED078 (profil_id), INDEX IDX_8D93D649D725330D (agence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caissier_compte (caissier_id INT NOT NULL, compte_id INT NOT NULL, INDEX IDX_56EAE243B514973B (caissier_id), INDEX IDX_56EAE243F2C56620 (compte_id), PRIMARY KEY(caissier_id, compte_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_agence_transaction (user_agence_id INT NOT NULL, transaction_id INT NOT NULL, INDEX IDX_C8DBC3BAD7C5BEE9 (user_agence_id), INDEX IDX_C8DBC3BA2FC0CB0F (transaction_id), PRIMARY KEY(user_agence_id, transaction_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agence ADD CONSTRAINT FK_64C19AA9F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE client_transaction ADD CONSTRAINT FK_737C20EA19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_transaction ADD CONSTRAINT FK_737C20EA2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF6526039622A97 FOREIGN KEY (admin_system_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBCF2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1659D30DE FOREIGN KEY (user_depot_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1D99F8396 FOREIGN KEY (user_retrait_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D11171DC20 FOREIGN KEY (client_envoi_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1EEAC783B FOREIGN KEY (client_retrait_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE caissier_compte ADD CONSTRAINT FK_56EAE243B514973B FOREIGN KEY (caissier_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE caissier_compte ADD CONSTRAINT FK_56EAE243F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_agence_transaction ADD CONSTRAINT FK_C8DBC3BAD7C5BEE9 FOREIGN KEY (user_agence_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_agence_transaction ADD CONSTRAINT FK_C8DBC3BA2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D725330D');
        $this->addSql('ALTER TABLE client_transaction DROP FOREIGN KEY FK_737C20EA19EB6921');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D11171DC20');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1EEAC783B');
        $this->addSql('ALTER TABLE agence DROP FOREIGN KEY FK_64C19AA9F2C56620');
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBCF2C56620');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F2C56620');
        $this->addSql('ALTER TABLE caissier_compte DROP FOREIGN KEY FK_56EAE243F2C56620');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649275ED078');
        $this->addSql('ALTER TABLE client_transaction DROP FOREIGN KEY FK_737C20EA2FC0CB0F');
        $this->addSql('ALTER TABLE user_agence_transaction DROP FOREIGN KEY FK_C8DBC3BA2FC0CB0F');
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF6526039622A97');
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBCA76ED395');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1659D30DE');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1D99F8396');
        $this->addSql('ALTER TABLE caissier_compte DROP FOREIGN KEY FK_56EAE243B514973B');
        $this->addSql('ALTER TABLE user_agence_transaction DROP FOREIGN KEY FK_C8DBC3BAD7C5BEE9');
        $this->addSql('DROP TABLE agence');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE client_transaction');
        $this->addSql('DROP TABLE commission');
        $this->addSql('DROP TABLE compte');
        $this->addSql('DROP TABLE depot');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE tarif');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE caissier_compte');
        $this->addSql('DROP TABLE user_agence_transaction');
    }
}
