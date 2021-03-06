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
    
    /**
     * @expectedException \App\Exceptions\InvalidAdditionException
     * @expectedExceptionMessage Maximum number of users reached
     */
    public function testItShouldThrowAnErrorIfTheMaximumNumberOfUsersHasBeenReached()
    {
        $client = static::createClient();
        
        //add 5 more users
        for ($i = 0; $i <= 5; $i++) {
          
            $newUser = ['firstname' => 'Tom'.$i, 
                        'lastname' => 'Cruise'.$i,
                        'email' => 'mission@impossible.net'.$i,
                        'department' => 'Test Dept - '.$i
                    ];
          
            $client->request(
                'POST',
                '/add',
                 $newUser
            );
          
        }

        $newUser = ['firstname' => 'Tom', 
            'lastname' => 'Cruise',
            'email' => 'mission@impossible.net',
            'department' => 'IM force'
        ];
        
        $client->request(
            'POST',
            '/add',
             $newUser
        );
        

        $response = $client->getResponse()->getContent(); 
      
    }
    
    /**
     * @expectedException \App\Exceptions\InvalidAdditionException
     * @expectedExceptionMessage Maximum number of users reached for department
     */
    public function testItShouldThrowAnErrorIfTheMaximumNumberOfUsersForADepartmentHasBeenReached()
    {
        $client = static::createClient();

        $newUser = ['firstname' => 'Tom', 
            'lastname' => 'Cruise',
            'email' => 'mission@impossible.net',
            'department' => 'Department'
        ];
        
        $client->request(
            'POST',
            '/add',
             $newUser
        );
        

        $response = $client->getResponse()->getContent(); 
      
    }
    
    /**
     * @expectedException \App\Exceptions\InvalidAdditionException
     * @expectedExceptionMessage Maximum number of users reached for department
     */
    public function testItShouldThrowAnErrorIfTheMaximumNumberOfUsersForADepartmentHasBeenReachedInAnUpdate()
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
            'department' => 'Department'
        ];
        
        $client->request(
            'POST',
            '/update',
             $updatedUser
        );
        
        $response = $client->getResponse()->getContent();
      
    }
    
    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Cannot update - user does not exist
     */
    public function testItShouldThrowAnErrorIfTheUserToBeUpdatedDoesntExist()
    {
        $client = static::createClient();
      
        $client->request('GET', '/users');
        $response = $client->getResponse()->getContent();
        $response = json_decode($response);
        
        $user = $response[0];
        
        $updatedUser = [
            'id' => $user->id+1000,
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
      
    }
    
    /**
     * @expectedException \App\Exceptions\InvalidAdditionException
     * @expectedExceptionMessage The data provided is incorrect
     */
    public function testItShouldThrowAnErrorIfAUserIsAddedAndTheDataIsntCorrect()
    {
        $client = static::createClient();

        $newUser = [];
        
        $client->request(
            'POST',
            '/add',
             $newUser
        );
        

        $response = $client->getResponse()->getContent(); 
      
    }
    
    /**
     * @expectedException \App\Exceptions\InvalidAdditionException
     * @expectedExceptionMessage The data provided is incorrect. No id provided
     */
    public function testItShouldThrowAnErrorIfAUserIsDeletedAndTheDataIsntCorrect()
    {
        $client = static::createClient();

        
        $client->request(
            'POST',
            '/delete',
            []
        );
        

        $response = $client->getResponse()->getContent(); 
      
    }
    
    /**
     * @expectedException \App\Exceptions\InvalidAdditionException
     * @expectedExceptionMessage The data provided is incorrect. No id provided
     */
    public function testItShouldThrowAnErrorIfAUserIsUpdatedAndTheDataIsntCorrect()
    {
        $client = static::createClient();

        
        $client->request(
            'POST',
            '/update',
            []
        );
        

        $response = $client->getResponse()->getContent(); 
      
    }
    
    
}