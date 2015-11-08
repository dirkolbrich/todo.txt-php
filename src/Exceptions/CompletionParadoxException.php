<?php

namespace TodoTxt\Exceptions;

class CompletionParadoxException extends \Exception
{
    public function __construct()
    {
        $this->message = 'The task was completed before it was created.';
    }
}
