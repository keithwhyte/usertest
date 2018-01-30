# User Service

## Purpose


### Setup


### Creation

Run the following
- composer create-project symfony/skeleton usertest
- cd usertest/
- composer require server —dev
- composer require doctrine maker;
- composer require --dev phpunit;
- composer require --dev browser-kit;
- composer require --dev doctrine/doctrine-fixtures-bundle;
- composer require annotations;

Add database config
- Change the DATABASE_URL to your production database in the .env file 
    DATABASE_URL=mysql://root:root@localhost:3306/usertest

Generate the database
- php bin/console doctrine:database:create
- php bin/console make:entity User

Add fields to generated Entity

Generate database information for table
- php bin/console doctrine:migrations:diff
- php bin/console doctrine:migrations:migrate

Note, the 2nd command will ask you to confirm table creation.

Create App Fixtures
- create fixtures file AppFixtures in src/DataFixtures/AppFixtures
- add test database config to phpunit.xml.dist
  <env name="DATABASE_URL" value="mysql://root:root@localhost:3306/9xb_test" />
- generate schema information for test
  php bin/console doctrine:schema:create --dump-sql > dump.sql
- create test schema
  mysql -u root -proot -h localhost -e "CREATE DATABASE user_test"
- import into test schema
  mysql -u root -proot -h localhost user_test < dump.sql

Note - some versions of mysql dump command add unnecessary comments to the dump.sql. It needs to be removed before it will run successfully.

Create controllers
- composer require annotations
- php bin/console make:controller UserController


### Pre-requisites 

- composer
- mamp server
