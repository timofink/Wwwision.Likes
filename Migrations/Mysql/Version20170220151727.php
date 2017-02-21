<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170220151727 extends AbstractMigration
{

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Initial migration, adding the "Like" ReadModel';
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('CREATE TABLE wwwision_likes_like (subjecttype VARCHAR(255) NOT NULL, userid VARCHAR(255) NOT NULL, subjectid VARCHAR(255) NOT NULL, PRIMARY KEY(subjecttype, userid, subjectid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('DROP TABLE wwwision_likes_like');
    }
}