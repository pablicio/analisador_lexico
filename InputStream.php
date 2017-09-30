<?php

require_once 'Lexer.php';
require_once 'Parser.php';
require_once 'Tokenizer.php';
require_once 'TokenReader.php';
require_once 'Token.php';
require_once 'LexerError.php';
require_once 'ParserError.php';

// Moldamos a entrada de dados. Transformamos a entrada do usuário e repassamos
// ao lexer ou ao lexer -> parser.
class InputStream
{
  const TYPE_FILE     = 0x0;
  const TYPE_TEXT     = 0x1;

  const METHOD_TREE   = 0x0;
  const METHOD_TOKEN  = 0x1;

  private $options = [];
  private $source;

  public function __construct($source, $options)
  {
    $this->options = $options;

    switch ($options["type"]) {
      case self::TYPE_FILE:
        $this->file($source);
        break;
      case self::TYPE_TEXT:
        $this->source = $source;
        $this->text();
        break;
      default:
        echo ">>> Especifique um tipo válido de saída (TYPE_FILE, TYPE_TEXT)", PHP_EOL;
        exit;
    }
  }

  private function file($file)
  {
    if (file_exists($file)) {
      $this->source = file_get_contents($file);
      $this->text();
    } else {
      echo ">>>> Arquivo {$file} não encontrado", PHP_EOL;
      exit;
    }
  }

  private function text()
  {
    switch ($this->options["method"]) {
      case InputStream::METHOD_TOKEN:
        $this->token();
        break;
      case InputStream::METHOD_TREE:
        $this->tree();
        break;
      default:
        echo ">>> Especifique um método válido (METHOD_TREE, METHOD_TOKEN)";
        exit;
    }
  }

  private function token()
  {
    try {
      $lexer = new Tokenizer($this->source);
      $token = $lexer->nextToken();

      while ($token->key !== Tokenizer::EOF_TYPE) {
        echo $token;
        $token = $lexer->nextToken();
      }
    } catch (LexerError $e) {
      echo $e->getMessage();
    }
  }

  private function tree()
  {
    try {
      $lexer = new Tokenizer($this->source);
      $parser = new TokenReader($lexer);
      $parser->arithmetic();
      $parser->printExpressionTree();
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }
}
