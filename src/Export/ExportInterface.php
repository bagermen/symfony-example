<?php

namespace App\Export;

/**
 * Generate file
 */
interface ExportInterface
{
    /**
     * Main function
     * @param array $data
     */
    public function generate(array $data = null);
}
