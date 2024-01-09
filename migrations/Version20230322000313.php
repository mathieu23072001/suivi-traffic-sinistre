<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230322000313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE declaration (id SERIAL NOT NULL, utilisateur_id INT NOT NULL, date_declaration TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, libelle VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, lieu VARCHAR(255) NOT NULL, published BOOLEAN NOT NULL, date_publication TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_fin_publication TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, longitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION DEFAULT NULL, dtype VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7AA3DAC2FB88E14F ON declaration (utilisateur_id)');
        $this->addSql('CREATE TABLE image (id INT NOT NULL, declaration_id INT NOT NULL, file_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C53D045FC06258A3 ON image (declaration_id)');
        $this->addSql('CREATE TABLE information_utile (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE position_geographique (id SERIAL NOT NULL, utilisateur_id INT NOT NULL, voie_id INT DEFAULT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, date_position TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, actif BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A705230FB88E14F ON position_geographique (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_A705230EAAC89CF ON position_geographique (voie_id)');
        $this->addSql('CREATE TABLE profil (id SERIAL NOT NULL, code VARCHAR(31) NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sinistre (id INT NOT NULL, type_sinistre_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F5AC7A676A70B14E ON sinistre (type_sinistre_id)');
        $this->addSql('CREATE TABLE type_sinistre (id SERIAL NOT NULL, libelle VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE utilisateur (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, telephone VARCHAR(8) NOT NULL, nom VARCHAR(127) NOT NULL, prenoms VARCHAR(255) NOT NULL, abonnement_actif BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3E7927C74 ON utilisateur (email)');
        $this->addSql('CREATE TABLE utilisateur_profil (utilisateur_id INT NOT NULL, profil_id INT NOT NULL, PRIMARY KEY(utilisateur_id, profil_id))');
        $this->addSql('CREATE INDEX IDX_877B881CFB88E14F ON utilisateur_profil (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_877B881C275ED078 ON utilisateur_profil (profil_id)');
        $this->addSql('CREATE TABLE voie (id SERIAL NOT NULL, id_osm BIGINT NOT NULL, nom VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A57CE978157F8A85 ON voie (id_osm)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE declaration ADD CONSTRAINT FK_7AA3DAC2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FC06258A3 FOREIGN KEY (declaration_id) REFERENCES declaration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE information_utile ADD CONSTRAINT FK_C3FC2A6ABF396750 FOREIGN KEY (id) REFERENCES declaration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE position_geographique ADD CONSTRAINT FK_A705230FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE position_geographique ADD CONSTRAINT FK_A705230EAAC89CF FOREIGN KEY (voie_id) REFERENCES voie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sinistre ADD CONSTRAINT FK_F5AC7A676A70B14E FOREIGN KEY (type_sinistre_id) REFERENCES type_sinistre (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sinistre ADD CONSTRAINT FK_F5AC7A67BF396750 FOREIGN KEY (id) REFERENCES declaration (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utilisateur_profil ADD CONSTRAINT FK_877B881CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utilisateur_profil ADD CONSTRAINT FK_877B881C275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE image_id_seq CASCADE');
        $this->addSql('ALTER TABLE declaration DROP CONSTRAINT FK_7AA3DAC2FB88E14F');
        $this->addSql('ALTER TABLE image DROP CONSTRAINT FK_C53D045FC06258A3');
        $this->addSql('ALTER TABLE information_utile DROP CONSTRAINT FK_C3FC2A6ABF396750');
        $this->addSql('ALTER TABLE position_geographique DROP CONSTRAINT FK_A705230FB88E14F');
        $this->addSql('ALTER TABLE position_geographique DROP CONSTRAINT FK_A705230EAAC89CF');
        $this->addSql('ALTER TABLE sinistre DROP CONSTRAINT FK_F5AC7A676A70B14E');
        $this->addSql('ALTER TABLE sinistre DROP CONSTRAINT FK_F5AC7A67BF396750');
        $this->addSql('ALTER TABLE utilisateur_profil DROP CONSTRAINT FK_877B881CFB88E14F');
        $this->addSql('ALTER TABLE utilisateur_profil DROP CONSTRAINT FK_877B881C275ED078');
        $this->addSql('DROP TABLE declaration');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE information_utile');
        $this->addSql('DROP TABLE position_geographique');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE sinistre');
        $this->addSql('DROP TABLE type_sinistre');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateur_profil');
        $this->addSql('DROP TABLE voie');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
