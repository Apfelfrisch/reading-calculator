<?php

namespace Proengeno\ReadingCalculator\Profiles;

use DateTime;
use function Proengeno\ReadingCalculator\sigmoid;

class GasCoefficientProfile implements Profile
{
    const C9 = 40;

    protected $coefficients;

    public function __construct(float $a, float $b, float $c, float $d, float $v)
    {
        $this->coefficients = compact('a', 'b', 'c', 'd', 'v');
    }

    public function addEntry(DateTime $start, float $temperature)
    {
        $this->entries[$start->format('Y-m-d H:i')] = sigmoid(
            $this->coefficients['a'],
            $this->coefficients['b'],
            $this->coefficients['c'],
            $this->coefficients['d'],
            $this->coefficients['v'],
            $temperature,
            self::C9
        );
    }

    public function getPeriodeFactor(DateTime $from, DateTime $until): float
    {
        $from = (clone $from)->modify('-1 day');

        $sumFactor = 0;
        while ($from->modify('+1 day')->format('Ymd') <= $until->format('Ymd')) {
            $sumFactor += $this->getFactor($from);
        }

        return $sumFactor;
    }

    public function yearlyFactor(DateTime $targetDate): float
    {
        return $this->getPeriodeFactor((clone $targetDate)->modify('-1 year + 1 day'), $targetDate);
    }

    private function getFactor($date): float
    {
        if (null !== $factor = $this->entries[$date->format('Y-m-d H:i')] ?? null) {
            return $factor;
        }
        throw new \Exception("No Profile Factor for " . $date->format('Y-m-d H:i'));
    }
}
