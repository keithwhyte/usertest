<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Exceptions\InvalidAdditionException;
use App\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/users", name="users")
     */
    public function index()
    {
      return new Response(
          json_encode($this->getList())
      );
    }
    
    private function getList()
    {
      
      $users = $this->getDoctrine()
          ->getRepository(User::class)
          ->createQueryBuilder('user')
          ->select('user')
          ->where('user.active = true')
          ->getQuery()
          ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
      
      if (!$users) {
          throw $this->createNotFoundException(
              'No users found.'
          );
      } else {
          return $users;
      }
      
    }
    
    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request)
    {
      $this->checkUserNumbers();
      $this->validateDepartment($request->request->get('department'));
     
      $em = $this->getDoctrine()->getManager();
      
      $user = new User();
      $user->setFirstName($request->request->get('firstname'));
      $user->setLastName($request->request->get('lastname'));
      $user->setEmail($request->request->get('email'));
      $user->setDepartment($request->request->get('department'));
      $user->setActive(true);
      
      $em->persist($user);
      
      $em->flush();
     
      return new Response(
          json_encode($this->getList())
      );
    }
    
    /**
     * @Route("/delete", name="delete")
     */
    public function delete(Request $request)
    {
      $id = $request->request->get('id');
      
      $em = $this->getDoctrine()->getManager();
 
      $user = $this->getDoctrine()
        ->getRepository(User::class)
        ->find($id);
   
      if (!$user) {
          throw $this->createNotFoundException(
              'Cannot delete - user does not exist'
          );
      } else {
          $user->setActive(false);
      
          $em->persist($user);

          $em->flush();

          return new Response(
              json_encode($this->getList())
          ); 
      }        
    } 
    
    /**
     * @Route("/update", name="update")
     */
    public function update(Request $request)
    {
      $this->validateDepartment($request->request->get('department'));
      $id = $request->request->get('id');

      $em = $this->getDoctrine()->getManager();
 
      $user = $this->getDoctrine()
        ->getRepository(User::class)
        ->find($id);
   
      if (!$user) {
          throw $this->createNotFoundException(
              'Cannot update - user does not exist'
          );
      } else {
          $user->setFirstName($request->request->get('firstname'));
          $user->setLastName($request->request->get('lastname'));
          $user->setEmail($request->request->get('email'));
          $user->setDepartment($request->request->get('department'));
      
          $em->persist($user);

          $em->flush();

          return new Response(
              json_encode($this->getList())
          ); 
      }
      
    }
    
    private function checkUserNumbers()
    {
      if (count($this->getList()) >= 10) {
        
        throw new InvalidAdditionException(
                'Maximum number of users reached'
        );
      }
    }
    
    private function validateDepartment($department)
    {
      $currentUsers = $this->getList();
      $departmentArray = [];
      foreach ($currentUsers as $user) {
        $departmentArray[$user['department']][] = $user['id'];
      }
      
      if (isset($departmentArray[$department]) && count($departmentArray[$department]) >= 4) 
      {
          throw new InvalidAdditionException(
                  'Maximum number of users reached for department'
          );
      }
      
      return true;    
    }
}
