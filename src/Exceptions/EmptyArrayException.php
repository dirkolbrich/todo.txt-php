<?php

namespace TodoTxt\Exceptions;

class EmptyArrayException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Cannot parse an empty array as metadata.';
    }
}
