<?php

namespace TodoTxt\Exceptions;

class InvalidStringException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Cannot parse an invalid string.';
    }
}
