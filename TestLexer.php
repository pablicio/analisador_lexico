<?php

require_once 'Lexer.php';
require_once 'Tokenizer.php';
require_once 'Token.php';
require_once 'LexerError.php';

$source = <<<END
  1 + 1 / 2.3;
  2.3 * -(4) - 2;
END
;

try {
  // Instanciamos o lexer
  $lexer = new Tokenizer($source);
  // Começamos pedindo o primeiro token
  $token = $lexer->nextToken();

  // Enquanto houver tokens, vamos imprimindo
  // Não criamos uma tabela de símbolos porque não faria sentido se ela não
  // teria uma saída
  while ($token->key !== Tokenizer::EOF_TYPE) {
    echo $token;
    $token = $lexer->nextToken();
  }

} catch (LexerError $e) {
  echo $e->getMessage();
}
