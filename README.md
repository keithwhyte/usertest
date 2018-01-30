# User Service

### Purpose

A server-side solution to perform CRUD operations (Create, Read, Update and Delete) on a list of users and their department.


### Pre-requisites 

- composer
- mamp server

### Setup
* Start up MAMP Server
* Run a composer update (replicates part 1 of below)
* Set up the database connection (part 2 of below)
* Set up the test database and fixture (part 5 of below). Note the AppFixtures file has already been created
* run tests via ./bin/phpunit

### Creation

1. Run the following 
* composer create-project symfony/skeleton usertest
* cd usertest/
* composer require server —dev
* composer require doctrine maker;
* composer require --dev phpunit;
* composer require --dev browser-kit;
* composer require --dev doctrine/doctrine-fixtures-bundle;
* composer require annotations;


2. Add database config
- Change the DATABASE_URL to your production database in the .env file 
    DATABASE_URL=mysql://root:root@localhost:3306/usertest


3. Generate the database
- php bin/console doctrine:database:create
- php bin/console make:entity User


4. Add fields to generated Entity
Generate database information for table
- php bin/console doctrine:migrations:diff
- php bin/console doctrine:migrations:migrate

Note, the 2nd command will ask you to confirm table creation.


5. Create App Fixtures
- create fixtures file AppFixtures in src/DataFixtures/AppFixtures
- add test database config to phpunit.xml.dist
  <env name="DATABASE_URL" value="mysql://root:root@localhost:3306/user_test" />
- generate schema information for test
  php bin/console doctrine:schema:create --dump-sql > dump.sql
- create test schema
  mysql -u root -proot -h localhost -e "CREATE DATABASE user_test"
- import into test schema
  mysql -u root -proot -h localhost user_test < dump.sql

Note - some versions of mysql dump command add unnecessary comments to the dump.sql. It needs to be removed before it will run successfully.


6. Create controllers
- composer require annotations
- php bin/console make:controller UserController
