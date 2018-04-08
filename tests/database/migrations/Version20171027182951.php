<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\Role;

class Version20171027182951 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE users (id INTEGER NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, remember_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');

        $secret = bcrypt('secret');
        $this->addSql("INSERT INTO users (name, email, password) VALUES('testing user1', 'test1email@test.com', '$secret')");
        $this->addSql("INSERT INTO users (name, email, password) VALUES('testing user2', 'test2email@gmail.com', '$secret')");
        $this->addSql("INSERT INTO users (name, email, password) VALUES('testing user3', 'test3email@test.com', '$secret')");

        $this->addSql('CREATE TABLE role (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE role_user (user_id INTEGER NOT NULL, role_id INTEGER NOT NULL, PRIMARY KEY(user_id, role_id))');
        $this->addSql('CREATE INDEX IDX_332CA4DDA76ED395 ON role_user (user_id)');
        $this->addSql('CREATE INDEX IDX_332CA4DDD60322AC ON role_user (role_id)');

        $this->addSql(sprintf("INSERT INTO role (id, name) VALUES(%d, '%s')", Role::ROOT, Role::ROOT_NAME));
        $this->addSql(sprintf("INSERT INTO role (id, name) VALUES(%d, '%s')", Role::USER, Role::USER_NAME));
        $this->addSql(sprintf("INSERT INTO role_user (user_id, role_id) VALUES(%d, %d)", 1, Role::ROOT));
        $this->addSql(sprintf("INSERT INTO role_user (user_id, role_id) VALUES(%d, %d)", 2, Role::USER));
        $this->addSql(sprintf("INSERT INTO role_user (user_id, role_id) VALUES(%d, %d)", 3, Role::USER));


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE role_user');
    }
}
