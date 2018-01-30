<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
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
}
