<?php

namespace App\Import\Parser\Csv;

use App\Import\Parser\IteratorInterface;

/**
 * Read file with CSV format
 */
class Iterator implements IteratorInterface
{
  protected $handler;
  protected $delimiter = ";";
  protected $pointer = 0;
  protected $current;
  protected $offset;

  /**
   * @param $file
   * @param array $options
   *    delimiter = ';' delimiter
   *    offset    = 0 rows offset
   */
  function __construct($file, array $options = [])
  {
    $default = ['delimiter' => ';', 'offset' => 0];
    $params = array_replace($default, array_intersect_key($options, $default));

    $this->handler = fopen($file, 'r');
    $this->delimiter = $params['delimiter'];

    $this->offset = (int) $params['offset'];
    $this->resetToStart();
  }

  /**
   * @inheritdoc
   */
  public function current()
  {
    return $this->current;
  }

  /**
   * @inheritdoc
   */
  public function next()
  {
    $this->current = fgetcsv($this->handler, null, $this->delimiter);
    ++$this->pointer;
  }

  /**
   * @inheritdoc
   */
  public function key()
  {
    return $this->pointer;
  }

  /**
   * @inheritdoc
   */
  public function valid()
  {
    return $this->current !== false;
  }

  /**
   * @inheritdoc
   */
  public function rewind()
  {
    rewind($this->handler);
    $this->resetToStart();
  }

  private function resetToStart()
  {
    for ($i = 0; $i <= $this->offset; $i++) {
      $this->next();
    }
    $this->pointer = 0;
  }
}