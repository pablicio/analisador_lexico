<?php

abstract class Lexer
{
  const EOF      = -1;
  const EOF_TYPE =  1;

  public $input;
  public $position = 0;
  public $char;
  public $size;

  public function __construct($input)
  {
    // Inicializamos a entrada do programador e acumulamos o tamanho dela
    $this->input = $input;
    $this->size  = strlen($this->input);

    // Quando for vazio, simplesmente retornamos <EOF>
    if ($this->size === 0) {
      return new Token(self::EOF_TYPE, NULL, 0, 0);
    }

    // Quando tiver conteúdo, acessamos o índice 0.
    $this->char = $input[$this->position];
  }

  protected function isEnd()
  {
    // Quando chegamos ao fim do arquivo, isto é, <EOF>
    return $this->position >= strlen($this->input);
  }

  public function consume()
  {
    // Consumimos um caractere e avançamos a posição. Se não tiver mais caracteres,
    // terminamos a execução. Se tiver, redefinimos o índice da entrada.
    $this->position++;
    $this->char = $this->isEnd()
      ? /* then      */ self::EOF
      : /* otherwise */ $this->input[$this->position];
  }

  public abstract function nextToken();
  public abstract function tokenName($token_type);
}
