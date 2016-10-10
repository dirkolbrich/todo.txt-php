<?php

namespace TodoTxt\Exceptions;

class InvalidArrayException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Cannot parse an invalid array as metadata.';
    }
}
