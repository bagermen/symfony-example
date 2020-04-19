<?php
namespace App\Import\Parser\Exception;

class ReadException extends \LogicException
{
  public function __construct($msg = 'Can\'t get data', $code = 400, $previous = null)
  {
    parent::__construct($msg, $code, $previous);
  }
}