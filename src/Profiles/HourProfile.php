<?php

namespace Proengeno\ReadingCalculator\Profiles;

use DateTime;

class HourProfile implements Profile
{
    protected array $entries = [];

    public function addEntry(DateTime $start, float $factor): void
    {
        $this->entries[$start->format('Y-m-d H:i')] = $factor;
    }

    public function getPeriodeFactor(DateTime $from, DateTime $until): float
    {
        return 0.0;
    }

    public function yearlyFactor(DateTime $targetDate): float
    {
        return 0.0;
    }
}
