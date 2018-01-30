<?php

namespace App\Test\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Controller\UserController;

class UserControllerTest extends WebTestCase
{
  
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $loader = new Loader();
        $loader->addFixture(new AppFixtures);

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());

        parent::setUp();
    }
    
    public function testItShouldReturnTheCorrectHTTPStatus()
    {
        $client = static::createClient();

        $client->request('GET', '/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testItShouldReturnACorrectListOfTheCurrentUsers()
    {
        $client = static::createClient();

        $client->request('GET', '/users');

        $response = $client->getResponse()->getContent();
        $response = json_decode($response);
        
        $this->assertEquals(5, count($response));
    }
    
    public function testItShouldAddANewUserCorrectly()
    {
        $client = static::createClient();

        $newUser = ['firstname' => 'Tom', 
            'lastname' => 'Cruise',
            'email' => 'mission@impossible.net',
            'department' => 'Test Department'
        ];
        
        $client->request(
            'POST',
            '/add',
             $newUser
        );

        $response = $client->getResponse()->getContent();
        $response = json_decode($response);
        $lastUser = end($response);
        
        $this->assertEquals(6, count($response));
        $this->assertEquals($newUser['firstname'], $lastUser->firstName);
        $this->assertEquals($newUser['lastname'], $lastUser->lastName);
        $this->assertEquals($newUser['email'], $lastUser->email);
        $this->assertEquals($newUser['department'], $lastUser->department);
 
    }
    
    public function testItShouldDeleteANewUserCorrectly()
    {
        $client = static::createClient();
      
        $client->request('GET', '/users');
        $response = $client->getResponse()->getContent();
        $response = json_decode($response);
        
        $user = $response[0];

        $client->request(
              'POST',
              '/delete',
               ['id' => $user->id]
          );

          $response = $client->getResponse()->getContent();
          $response = json_decode($response);

          $this->assertEquals(4, count($response));

    }
    
    public function testItShouldUpdateTheUserCorrectly()
    {
        $client = static::createClient();
      
        $client->request('GET', '/users');
        $response = $client->getResponse()->getContent();
        $response = json_decode($response);
        
        $user = $response[0];
        
        $updatedUser = [
            'id' => $user->id,
            'firstname' => 'Tom', 
            'lastname' => 'Cruise',
            'email' => 'mission@impossible.net',
            'department' => 'IM force'
        ];
        
        $client->request(
            'POST',
            '/update',
             $updatedUser
        );
        
        $response = $client->getResponse()->getContent();
        $response = json_decode($response);
        
        
        foreach ($response as $responseUser) {
          if ($user->id == $responseUser->id) {
              $updatedResponseUser = $responseUser;
          }
        }
        
        $this->assertEquals($updatedUser['firstname'], $updatedResponseUser->firstName);
        $this->assertEquals($updatedUser['lastname'], $updatedResponseUser->lastName);
        $this->assertEquals($updatedUser['email'], $updatedResponseUser->email);
        $this->assertEquals($updatedUser['department'], $updatedResponseUser->department);
        
    }
}