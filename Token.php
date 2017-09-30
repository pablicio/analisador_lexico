<?php

class Token
{
  public $key;
  public $value;
  public $line;
  public $column;

  public function __construct($key, $value = NULL, $line = NULL, $column = NULL)
  {
    $this->key   = $key;
    $this->value = $value;
    $this->line = $line;
    $this->column = $column;
  }

  public function containsValue()
  {
    return $this->value !== NULL;
  }

  public function __toString()
  {
    $token_name = Tokenizer::$token_names[$this->key];

    return ($this->containsValue()
      ? "<{$token_name}, {$this->value}>"
      : "<{$token_name}>") . PHP_EOL;
  }
}
