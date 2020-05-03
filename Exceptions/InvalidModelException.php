<?php

namespace Geeklearners\Exceptions;

use Exception;

class InvalidModelException extends Exception
{

    public function __construct($message)
    {
        parent::__construct($message);
    }
}
