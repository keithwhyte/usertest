<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstName;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastName;
    
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;
    
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $department;
    
    /** @ORM\Column(type="boolean", options={"default":true, "comment":"boolean to describe status. true=active, false=inactive/deleted"}) */
    private $active;
    
    /**
     * Get the ID of a user
     * @return int
     */
    public function getId() {
      return $this->id;
    }

    /**
     * Get the first name of a user
     * @return string
     */
    public function getFirstName() {
      return $this->firstName;
    }

    /**
     * Get the last name of a user
     * @return string
     */
    public function getLastName() {
      return $this->lastName;
    }

    /**
     * Get the email address of a user
     * @return string
     */
    public function getEmail() {
      return $this->email;
    }

    /**
     * Get the department of a user
     * @return int
     */
    public function getDepartment() {
      return $this->department;
    }

    /**
     * Get the active status of a user
     * @return boolean
     */
    public function getActive() {
      return $this->active;
    }

    /**
     * Set the first name of a user
     * @param string $firstName
     */
    public function setFirstName($firstName) {
      $this->firstName = $firstName;
    }

    /**
     * Set the last name of a user
     * @param string $lastName
     */
    public function setLastName($lastName) {
      $this->lastName = $lastName;
    }

    /**
     * Set the email address of a user
     * @param string $email
     */
    public function setEmail($email) {
      $this->email = $email;
    }

    /**
     * Set the department of a user
     * @param int $department
     */    
    public function setDepartment($department) {
      $this->department = $department;
    }

    /**
     * Set the active status of a user
     * @param boolean $active
     */    
    public function setActive($active) {
      $this->active = $active;
    }
}
