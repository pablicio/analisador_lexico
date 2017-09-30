<?php

class ParserError extends Exception
{
  public function __construct($message, $line = 0, $column = 0)
  {
    $composed_message  = ">>>> ParserError: {$message}" . PHP_EOL;
    $composed_message .= "     Na linha:    {$line}" . PHP_EOL;
    $composed_message .= "     Na coluna:   {$column}" . PHP_EOL;
    parent::__construct($composed_message);
  }
}
