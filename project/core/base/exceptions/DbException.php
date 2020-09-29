<?php


namespace base\exceptions;

use base\controller\traits\BaseTrait;

class DbException extends \Exception
{

    protected $messages;

    use BaseTrait;

    public function __construct($message = '', $code = 0)
    {
        parent::__construct($message, $code);

        $this->messages = include("messeges.php");
        $error = $this->getMessage() ? $this->getMessage() : $this->messages[$this->getCode()];
        $error .= "\r\n" . 'file ' . $this->getFile() . "\r\n" . 'In line ' . $this->getLine() . "\r\n";

        // if($this->messages[$this->getCode()]) $this->message = $this->messages[$this->getCode()];

        $this->writeLog($error, 'db_log.txt');
    }

}