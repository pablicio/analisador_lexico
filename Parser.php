<?php

abstract class Parser
{
  public $source;
  public $lookahead;

  public function __construct(Tokenizer $source)
  {
    $this->source = $source;
    $this->consume();
  }

  public function match()
  {
    // Casamos um token. Essa função é variádica. Pode receber múltiplos
    // parâmetros. Damos mensagens de erro concisas, caso falhe.
    $args = func_get_args();
    foreach ($args as $arg) {
      if ($this->lookahead->key === $arg) {
        $data = $this->lookahead->value !== NULL
          ? $this->lookahead->value
          : $this->lookahead->key;
        $this->consume();
        return $data;
      }
    }

    $expected = array_map(function($t) {
      return Tokenizer::$token_names[$t];
    }, $args);

    $found = Tokenizer::$token_names[$this->lookahead->key];
    $line = $this->lookahead->line;
    $column = $this->lookahead->column;

    $message = "Esperava encontrar " . implode(" ou ", $expected)
      . ". Encontrou {$found}";

    throw new ParserError($message, $line, $column);
  }

  public function consume()
  {
    // Consumimos um token
    $this->lookahead = $this->source->nextToken();
  }
}
