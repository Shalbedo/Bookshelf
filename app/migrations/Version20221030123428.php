<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221030123428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "order_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "order" (id INT NOT NULL, owner_id INT NOT NULL, status VARCHAR(255) NOT NULL, estimated_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, price DOUBLE PRECISION NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F52993987E3C61F9 ON "order" (owner_id)');
        $this->addSql('CREATE TABLE order_book (order_id INT NOT NULL, book_id INT NOT NULL, PRIMARY KEY(order_id, book_id))');
        $this->addSql('CREATE INDEX IDX_861499268D9F6D38 ON order_book (order_id)');
        $this->addSql('CREATE INDEX IDX_8614992616A2B381 ON order_book (book_id)');
        $this->addSql('CREATE TABLE user_book (user_id INT NOT NULL, book_id INT NOT NULL, PRIMARY KEY(user_id, book_id))');
        $this->addSql('CREATE INDEX IDX_B164EFF8A76ED395 ON user_book (user_id)');
        $this->addSql('CREATE INDEX IDX_B164EFF816A2B381 ON user_book (book_id)');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F52993987E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_book ADD CONSTRAINT FK_861499268D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_book ADD CONSTRAINT FK_8614992616A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_book ADD CONSTRAINT FK_B164EFF8A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_book ADD CONSTRAINT FK_B164EFF816A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "order_id_seq" CASCADE');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F52993987E3C61F9');
        $this->addSql('ALTER TABLE order_book DROP CONSTRAINT FK_861499268D9F6D38');
        $this->addSql('ALTER TABLE order_book DROP CONSTRAINT FK_8614992616A2B381');
        $this->addSql('ALTER TABLE user_book DROP CONSTRAINT FK_B164EFF8A76ED395');
        $this->addSql('ALTER TABLE user_book DROP CONSTRAINT FK_B164EFF816A2B381');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP TABLE order_book');
        $this->addSql('DROP TABLE user_book');
    }
}
