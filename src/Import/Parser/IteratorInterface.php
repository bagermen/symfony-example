<?php
namespace App\Import\Parser;

/**
 * Common Iterator interface
 */
interface IteratorInterface extends \Iterator
{
    public function __construct($file, array $options = []);
}