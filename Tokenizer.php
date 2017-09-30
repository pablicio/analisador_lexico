<?php

class Tokenizer extends Lexer
{
  const T_INTEGER        = 2;
  const T_DOUBLE         = 3;
  const T_SEMICOLON      = 4;
  const T_IDENTIFIER     = 5;
  const T_LPAREN         = 6;
  const T_RPAREN         = 7;
  const T_PLUS           = 8;
  const T_MINUS          = 9;
  const T_DIVISION       = 10;
  const T_MULTIPLICATION = 11;

  // Linha e coluna inicial
  public $line = 1;
  public $column = 0;

  // Nomes dos tokens de acordo com o índice das constantes
  static $token_names = [
    'n/a', '<EOF>', 'T_INTEGER', 'T_DOUBLE', 'T_SEMICOLON', 'T_IDENTIFIER'
  , 'T_LPAREN', 'T_RPAREN', 'T_PLUS', 'T_MINUS', 'T_DIVISION'
  , 'T_MULTIPLICATION'
  ];

  public function __construct($input)
  {
    parent::__construct($input);
  }

  // Enquanto não chegarmos ao fim do arquivo, vamos analisando caractere por
  // caractere, consumindo e retornando tokens, como generators
  public function nextToken()
  {
    if ($this->size === 0) {
      return new Token(self::EOF_TYPE, NULL, 0, 0);
    }

    while ($this->char != self::EOF) {
      switch ($this->char) {
        case " ":
        case "\t":
        case "\r\n":
        case "\n":
        case "\r":
        case PHP_EOL:
          $this->skipBlank();
          continue;
        case "+":
          $this->consume(); $this->column++;
          return new Token(self::T_PLUS, NULL, $this->line, $this->column);
        case "-":
          $this->consume(); $this->column++;
          return new Token(self::T_MINUS, NULL, $this->line, $this->column);
        case "*":
          $this->consume(); $this->column++;
          return new Token(self::T_MULTIPLICATION, NULL, $this->line, $this->column);
        case "/":
          $this->consume(); $this->column++;
          return new Token(self::T_DIVISION, NULL, $this->line, $this->column);
        case "(":
          $this->consume(); $this->column++;
          return new Token(self::T_LPAREN, NULL, $this->line, $this->column);
        case ")":
          $this->consume(); $this->column++;
          return new Token(self::T_RPAREN, NULL, $this->line, $this->column);
        case ";":
          $this->consume(); $this->column++;
          return new Token(self::T_SEMICOLON, NULL, $this->line, $this->column);
        default:
          if (ctype_digit($this->char)) {
            return $this->digit();
          }

          if (ctype_alpha($this->char)) {
            return $this->identifier();
          }

          throw new LexerError("Caractere inesperado: {$this->char}", $this->line, $this->column);
      }
    }

    return new Token(self::EOF_TYPE);
  }

  public function tokenName($token_type)
  {
    return static::$token_names[$token_type];
  }

  private function skipBlank()
  {
    // Quando alcançarmos uma quebra de linha, adicionamos +1 no nosso contador
    while (ctype_space($this->char)) {
      switch ($this->char) {
        case "\r\n":
        case "\n":
        case "\r":
        case PHP_EOL:
          $this->line++;
          $this->column = 0;
          $this->consume();
          break;
        default:
          $this->column++;
          $this->consume();
          break;
      }
    }
  }

  private function digit()
  {
    // Casamos números de ponto flutuante e inteiros. Adicionamos o tamanho do
    // buffer ao número de colunas.
    $buffer = [$this->char];
    $this->consume();
    $type = 'integer';


    hold_number:
      while (ctype_digit($this->char)) {
        $buffer[] = $this->char;
        $this->consume();
      }

    if ($type !== 'double' && $this->char === ".") {
      $type = 'double';
      $buffer[] = ".";
      $this->consume();
      goto hold_number;
    }

    // Juntamos os itens do array em uma string, por otimização. Strings
    // tem alto custo de concatenação
    $buffer = implode($buffer);

    $this->column += sizeof($buffer);

    return $type === 'integer'
      ? new Token(self::T_INTEGER, (int) $buffer, $this->line, $this->column)
      : new Token(self::T_DOUBLE, (double) $buffer, $this->line, $this->column);

  }

  private function identifier()
  {
    // Casamos identificadores e armazenamos em um buffer, somente por questão
    // de dar melhores mensagens de erro
    $buffer = [$this->char];
    $this->consume();

    while (ctype_alpha($this->char)) {
      $buffer[] = $this->char;
      $this->consume();
    }

    $buffer = implode($buffer);

    $this->column += sizeof($buffer);

    return new Token(self::T_IDENTIFIER, $buffer, $this->line, $this->column);
    exit;
  }
}
