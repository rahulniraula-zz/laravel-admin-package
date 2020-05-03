<?php

namespace Geeklearners\Exceptions;

use Exception;

class FieldsNotDeclaredException extends Exception
{

    public function __construct($message)
    {
        parent::__construct($message);
    }
}
