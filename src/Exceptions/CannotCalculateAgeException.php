<?php

namespace TodoTxt\Exceptions;

class CannotCalculateAgeException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Cannot calculate age of a task without a creation date';
    }
}
