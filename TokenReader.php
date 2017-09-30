<?php

class TokenReader extends Parser
{
  private $expression_tree = [];

  public function __construct(Tokenizer $source)
  {
    parent::__construct($source);
  }

  public function arithmetic()
  {

    // Aceitamos várias <expr>; <expr>; ...
    // Usamos um predictive top down recursive descent parser para fazer a
    // análise semântica e, sem recursividade à esquerda, trabalhar na
    // precedência dos operadores. Tivemos, como custo, dificuldade na geração
    // da AST com operadores binários seguindo a notação polonesa, mas tivemos
    // facilidade, como teríamos em uma PEG.

    while ($this->lookahead->key !== Tokenizer::EOF_TYPE) {
      $this->expression_tree[] = $this->expr();
      $this->match(Tokenizer::T_SEMICOLON);
    }
  }

  public function printExpressionTree()
  {
    var_dump($this->expression_tree);
  }

  private function isSecondaryOperator()
  {
    return $this->lookahead->key === Tokenizer::T_PLUS
        || $this->lookahead->key === Tokenizer::T_MINUS;
  }

  private function isPrimaryOperator()
  {
    return $this->lookahead->key === Tokenizer::T_MULTIPLICATION
        || $this->lookahead->key === Tokenizer::T_DIVISION;
  }

  public function digit()
  {
    $unary = NULL;
    if ($this->isSecondaryOperator()) {
      $unary = $this->secondaryOperator();
    }

    $number = $this->match(Tokenizer::T_INTEGER, Tokenizer::T_DOUBLE);

    return $unary === NULL
      ? $number
      : ($unary === Tokenizer::T_MINUS
        ? -$number
        : $number);
  }

  public function primaryOperator()
  {
    return $this->match(Tokenizer::T_DIVISION, Tokenizer::T_MULTIPLICATION);
  }

  public function secondaryOperator()
  {
    return $this->match(Tokenizer::T_PLUS, Tokenizer::T_MINUS);
  }

  public function expr()
  {
    $unary = NULL;
    if ($this->isSecondaryOperator()) {
      $unary = $this->secondaryOperator();
    }

    $x = $this->term();

    if ($unary !== NULL && $unary === Tokenizer::T_MINUS) {
      if (is_array($x)) {
        $x["left"] = -$x["left"];
      } else {
        $x = -$x;
      }
    }

    $xs = [];

    while ($this->isSecondaryOperator()) {
      $unary    = NULL;
      $operator = $this->secondaryOperator();

      if ($this->isSecondaryOperator()) {
        $unary = $this->secondaryOperator();
      }

      $term = $this->term();

      if ($unary !== NULL && $unary === Tokenizer::T_MINUS) {
        $term = -$term;
      }

      $xs[] = ["operator" => Tokenizer::$token_names[$operator], "operand" => $term];
    }

    return empty($xs)
      ? $x
      : ["left" => $x, "right" => $xs];
  }

  public function term()
  {


    $x  = $this->factor();
    $xs = [];

    while ($this->isPrimaryOperator()) {
      $unary    = NULL;
      $operator = $this->primaryOperator();

      if ($this->isSecondaryOperator()) {
        $unary = $this->secondaryOperator();
      }

      $factor   = $this->factor();

      // Quando houver operador unário negativo, modificamos o valor computado
      if ($unary !== NULL && $unary === Tokenizer::T_MINUS) {
        $unary = -$unary;
      }

      $xs[] = ["operator" => Tokenizer::$token_names[$operator], "operand" => $factor];
    }

    return empty($xs)
      ? $x
      : ["left" => $x, "right" => $xs];
  }

  public function factor()
  {
    if ($this->lookahead->key === Tokenizer::T_LPAREN) {
      $this->match(Tokenizer::T_LPAREN);
      $factor = $this->expr();
      $this->match(Tokenizer::T_RPAREN);
    } else {
      $factor = $this->digit();
    }

    return $factor;
  }
}
