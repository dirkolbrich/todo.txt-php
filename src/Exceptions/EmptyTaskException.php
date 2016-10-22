<?php

namespace TodoTxt\Exceptions;

class EmptyTaskException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Cannot use an empty task.';
    }
}
