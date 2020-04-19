<?php
namespace App;

trait Helpers
{
    /**
     * Random date
     */
    protected function randomDateInRange(\DateTime $start, \DateTime $end)
    {
        $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
        $randomDate = new \DateTime();
        $randomDate->setTimestamp($randomTimestamp);
        return $randomDate;
    }

    protected function strToDate(string $time)
    {
        return \DateTime::createFromFormat("Y-m-d H:i:s", $time);
    }

    protected function dateToStr(\DateTime $date): string
    {
        return $date->format("Y-m-d H:i:s");
    }

    protected function isCsv(string $path):bool
    {
        $pathinfo = pathinfo($path);

        return (isset($pathinfo['extension']) && strtolower($pathinfo['extension']) == 'csv');
    }
}