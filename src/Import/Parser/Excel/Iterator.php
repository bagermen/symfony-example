<?php

namespace App\Import\Parser\Excel;

use App\Helpers;
use App\Import\Parser\IteratorInterface;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\CellIterator;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;

/**
 * Iterator over Excel file
 * Read excel file as an array
 */
class Iterator implements IteratorInterface
{
  use Helpers;

  protected $chunk = 1;
  protected $file = '';
  protected $offset = 0;

  /** @var IReader */
  private $reader;

  /** @var RowFilter */
  private $filter;

  /** @var RowIterator  */
  private $batch = null;

  /** @var Worksheet */
  private $workSheet;

  private $queried = 0;

  private $keyIndex = -1;

  private $stateValid = true;

  private $targetSheet = '';

  /**
   * @param string $file Excel file
   * @param array $options Options:
   *    offset => 0 - numer of rows to not read from start
   *    sheet  => '' - name of sheet to read data from
   *    chunk  => 20 - piece of records which are read as one piece.
   * @throws Exception
   */
  public function __construct($file, array $options = [])
  {
    $default = ['offset' => 0, 'sheet' => '', 'chunk' => 20];
    $params = array_replace($default, array_intersect_key($options, $default));

    $this->file = (string) $file;
    $this->offset = (int) $params['offset'];
    $this->targetSheet = (string) $params['sheet'];
    $this->chunk = (int) $params['chunk'];

    $this->setQueried($this->offset);

    $this->filter = new RowFilter();
    $this->reader = IOFactory::createReaderForFile($file);
    $this->reader->setReadDataOnly(true);
    $this->reader->setReadFilter($this->filter);
  }

  /**
   * @return array
   */
  public function current()
  {
    if (!$this->getBatch()) {
      return null;
    }

    /** @var CellIterator $row */
    $row = $this->getBatch()->current()->getCellIterator();
    $row->setIterateOnlyExistingCells(false);

    $result = [];
    /** @var Cell $cell */
    foreach ($row as $key => $cell) {
      if (SharedDate::isDateTime($cell)) {
        $result[] = $this->dateToStr(SharedDate::excelToDateTimeObject($cell->getValue()));
      } else {
        $result[] = $cell->getValue();
      }
    }

    return $result;
  }

  /**
   * @inheritdoc
   */
  public function next()
  {
    if ($this->getBatch() instanceof RowIterator && $this->getBatch()->valid()) {
      $this->getBatch()->next();
      if (!$this->getBatch()->valid() || ($this->getBatch()->key() - $this->getQueried()) > 0) {
        if (!$this->loadExcelData()) {
          $this->setStateValid(false);
        }
      }
    } else if (!$this->loadExcelData()) {
      $this->setStateValid(false);
    }

    if ($this->isStateValid()) {
      $this->setKeyIndex($this->getKeyIndex() + 1);
    }
  }

  /**
   * @return int
   */
  public function key()
  {
    return $this->getKeyIndex();
  }

  /**
   * @return bool
   */
  public function valid()
  {
    if (!$this->getBatch()) {
      $this->next();
    }

    return $this->isStateValid();
  }

  /**
   * @inheritdoc
   */
  public function rewind()
  {
    $this->setBatch(null);
    $this->setStateValid(true);
    $this->setQueried($this->offset);
    $this->setKeyIndex(0);
  }

  /**
   * @return IReader
   */
  protected function getReader()
  {
    return $this->reader;
  }

  /**
   * @return RowIterator
   */
  protected function getBatch()
  {
    return $this->batch;
  }

  /**
   * @return int
   */
  protected function getQueried()
  {
    return $this->queried;
  }

  /**
   * @return RowFilter
   */
  protected function getFilter()
  {
    return $this->filter;
  }

  /**
   * @return int
   */
  protected function getKeyIndex()
  {
    return $this->keyIndex;
  }

  /**
   * @param boolean $stateValid
   */
  protected function setStateValid($stateValid)
  {
    $this->stateValid = $stateValid;
  }

  /**
   * @param int $keyIndex
   */
  protected function setKeyIndex($keyIndex)
  {
    $this->keyIndex = $keyIndex;
  }

  /**
   * @param RowIterator $batch
   */
  protected function setBatch($batch)
  {
    $this->batch = $batch;
  }

  /**
   * @param int $queried
   */
  protected function setQueried($queried)
  {
    $this->queried = $queried;
  }

  /**
   * @return boolean
   */
  protected function isStateValid()
  {
    return $this->stateValid;
  }

  /**
   * @return int
   */
  public function getTargetSheet()
  {
    return $this->targetSheet;
  }

  /**
   * @return Worksheet
   */
  protected function getWorkSheet()
  {
    return $this->workSheet;
  }

  /**
   * @param Worksheet $workSheet
   */
  protected function setWorkSheet($workSheet)
  {
    $this->workSheet = $workSheet;
  }

  /**
   * @param int $targetSheet
   */
  public function setTargetSheet($targetSheet)
  {
    $this->targetSheet = (string) $targetSheet;
  }

  private function loadExcelData()
  {
    $this->getFilter()->setRows($this->getQueried() + 1, $this->chunk);
    if ($this->getTargetSheet()) {
      $this->getReader()->setLoadSheetsOnly($this->getTargetSheet());
    }

    /** @var \PhpOffice\PhpSpreadsheet\Spreadsheet $wb */
    $wb = $this->getReader()->load($this->file);
    $wh = $wb->getActiveSheet();
    $this->setWorkSheet($wh);

    if ($wh->getHighestRow() - $this->getQueried() > 0) {
      $this->setBatch($wh->getRowIterator($this->getQueried() + 1));
      $this->setQueried($this->getQueried() + $this->chunk);

      return true;
    }

    return false;
  }
}