<?php

namespace TodoTxt\Exceptions;

class EmptyStringException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Cannot parse an empty string as a task.';
    }
}
