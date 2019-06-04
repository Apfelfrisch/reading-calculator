<?php

namespace Proengeno\ReadingCalculator\Profiles;

use DateTime;

interface Profile
{
    public function getPeriodeFactor(DateTime $from, DateTime $until): float;

    public function yearlyFactor(DateTime $targetDate): float;
}
