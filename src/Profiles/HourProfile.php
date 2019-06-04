<?php

namespace Proengeno\ReadingCalculator\Profiles;

use DateTime;

class HourProfile implements Profile
{
    public function addEntry(DateTime $start, $factor)
    {
        $this->entries[$start->format('Y-m-d H:i')] = $factor;
    }

    public function getPeriodeFactor(DateTime $from, DateTime $until): float
    {
        //
    }

    public function yearlyFactor(): float
    {
        //
    }
}
