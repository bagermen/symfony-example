<?php

namespace App\Import\Parser\Excel;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
/**
 * This filter allows to read required numbers of columns and rows
 */
class RowFilter implements IReadFilter
{
  private $startRow = 0;
  private $endRow = 0;
  private $columns = [];

  /**
   * @param int $startRow
   * @param int $chunkSize
   */
  public function setRows($startRow, $chunkSize = 0)
  {
    $this->startRow = $startRow;
    $this->endRow = $startRow + $chunkSize;
  }

  /**
   * @param int[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = (array) $columns;
  }

  /**
   * @inheritdoc
   */
  public function readCell($column, $row, $worksheetName = '')
  {
    $rowPassed = false;
    if ($row >= $this->startRow && $row < $this->endRow) {
      $rowPassed = true;
    }

    if (count($this->columns)) {
      $colsPassed = in_array($column, $this->columns);
    } else {
      $colsPassed = true;
    }

    return $rowPassed && $colsPassed;
  }
}