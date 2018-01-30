<?php

namespace App\Exceptions;

class InvalidAdditionException extends \Exception
{
    /**
     * @param string     $message  The internal exception message
     */
    public function __construct($message = null)
    {
        parent::__construct($message);
    }
}