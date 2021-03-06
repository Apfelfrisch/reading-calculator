<?php

namespace Proengeno\ReadingCalculator\Profiles;

use DateTime;
use function Proengeno\ReadingCalculator\sigmoid;

class GasCoefficientProfile implements Profile
{
    public const C9 = 40;

    /** @var array<string, float> */
    protected array $entries = [];

    /** @psalm-var array{a: float, b: float, c: float, c: float, d: float, v:float} */
    protected array $coefficients;

    public function __construct(float $a, float $b, float $c, float $d, float $v)
    {
        $this->coefficients = compact('a', 'b', 'c', 'd', 'v');
    }

    public function addEntry(DateTime $start, float $temperature): void
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
        $from = clone $from;

        $sumFactor = 0;
        while ($from->modify('+1 day')->format('Ymd') <= $until->format('Ymd')) {
            $sumFactor += $this->getFactor($from);
        }

        return $sumFactor;
    }

    public function yearlyFactor(DateTime $targetDate): float
    {
        return $this->getPeriodeFactor((clone $targetDate)->modify('-1 year'), $targetDate);
    }

    private function getFactor(DateTime $date): float
    {
        if (null !== $factor = $this->entries[$date->format('Y-m-d H:i')] ?? null) {
            return $factor;
        }
        throw new \Exception("No Profile Factor for " . $date->format('Y-m-d H:i'));
    }
}
