<?php

namespace App\Import\Parser;

use App\Import\Parser\Exception\ReadException;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use App\Import\Parser\Csv\Iterator as CsvIterator;
use App\Import\Parser\Excel\Iterator as ExcelIterator;

/**
 * Iterator over file
 * (THIS ITERATOR IS USED TO PROVIDE ADDITIONAL PARSING FUNCIONALITY BUT HERE IT IS JUST AN OUTER ITERATOR)
 * (File can be Csv or Excel)
 */
class Iterator implements \OuterIterator, IteratorInterface
{
    protected $file;

    protected $options;

    private $iterator;

    /**
     * @param string|\Iterator $file File name or Iterator
     * @param array $options
     *    offset = 0 number of rows which should not be read
     *
     *  (Other options look at:Model_Util_Parser_Excel_Iterator, Model_Util_Parser_Csv_Iterator)
     */
    public function __construct($file, array $options = [])
    {
        if ($file instanceof \Iterator) {
            $this->iterator = $file;
        } else {
            $this->file = (string) $file;
            $this->options = (array) $options;
        }
    }

    /**
     * @inheritdoc
     */
    public function getInnerIterator()
    {
        if (!$this->iterator) {
            $pathinfo = pathinfo($this->file);
            try {
                if (!is_readable($this->file)) {
                    throw new ReadException();
                }

                $class = (isset($pathinfo['extension']) && strtolower($pathinfo['extension']) == 'csv')
                    ? $this->getCsvIteratorClass()
                    : $this->getExcelIteratorClass();

                $this->iterator = new $class($this->file, $this->options);
            } catch (Exception $e) {
                throw new ReadException();
            }
        }

        return $this->iterator;
    }

    /**
     * CSV parser class name
     * @return string
     */
    public function getCsvIteratorClass()
    {
        return CsvIterator::class;
    }

    /**
     * Excel parse class name
     * @return string
     */
    public function getExcelIteratorClass()
    {
        return ExcelIterator::class;
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->getInnerIterator()->current();
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->getInnerIterator()->next();
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->getInnerIterator()->key();
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return $this->getInnerIterator()->valid();
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->getInnerIterator()->rewind();
    }
}