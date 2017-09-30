<?php

require_once 'InputStream.php';

// Recebemos os parâmetros do usuário e removemos o nome do programa
$args = $argv;
array_shift($args);

// Capturamos o tipo de entrada do programador
$type = @$args[1] === '-f' ? InputStream::TYPE_FILE : InputStream::TYPE_TEXT;

switch (@$args[0]) {
  case '-p':
    parser();
    break;
  case '-l':
    lexer();
    break;
  default:
    help();
}

// Quando vier um comando inválido, mostramos a ajuda
function help()
{
  echo <<<END
  >>> Analisador de expressões aritméticas
    para a disciplina de compiladores.

      Uso:

      php Compiler.php <tipo>

      <tipo>   -l (Lexer)   ou -p (Parser)
      <metodo> -f (Arquivo) ou -t (Texto)
END
, PHP_EOL;
exit;
}

// Ações para o parser
function parser()
{
  global $args, $type;
  new InputStream(@$args[2], [
    "type"   => $type,
    "method" => InputStream::METHOD_TREE
  ]);
}

// Ações para o lexer
function lexer()
{
  global $args, $type;
  new InputStream(@$args[2], [
    "type"   => $type,
    "method" => InputStream::METHOD_TOKEN
  ]);
}
