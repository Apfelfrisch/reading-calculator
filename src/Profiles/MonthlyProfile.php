<?php

namespace Proengeno\ReadingCalculator\Profiles;

use DateTime;

class MonthlyProfile implements Profile
{
    protected $entries = [];
    protected $keyFormat;

    public function __construct($keyFormat = 'Y-m')
    {
        $this->keyFormat = $keyFormat;
    }

    public static function fromElectricTemplates($path = __DIR__ . '/templates/electric/monthly/'): array
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException("Invalid path $path");
        }

        $instances = [];
        foreach (array_slice(scandir($path), 2) as $file) {
            $instances[strtoupper(pathinfo($file, PATHINFO_FILENAME))] = static::fromArray(include($path . $file), 'm');
        }

        return $instances;
    }

    public static function fromArray($entries, $keyFormat = 'Y-m'): Profile
    {
        $instance = new static($keyFormat);

        foreach ($entries as $entry) {
            $instance->addEntry($entry[0], $entry[1]);
        }

        return $instance;
    }

    public function addEntry(DateTime $start, float $factor)
    {
        $this->entries[$start->format($this->keyFormat)] = $factor;
    }

    public function getPeriodeFactor(DateTime $from, DateTime $until): float
    {
        $from = clone $from;

        $sumFactor = 0;

        // Angebrochener Monatsstart
        $sumFactor = $this->withinMonthFactor($from, (clone $from)->modify('last day of this month'));

        // Volle Zwischenmonate
        while ($from->modify('last day of +1 month') <= $until) {
            $sumFactor += $this->getFactor($from);
        }

        // Angebrochener Monatsschluß
        $sumFactor += $this->withinMonthFactor($from->modify('last day of -1 month'), $until);

        return $sumFactor;
    }

    public function yearlyFactor(DateTime $targetDate): float
    {
        return $this->getPeriodeFactor((clone $targetDate)->modify('-1 year'), $targetDate);
    }

    private function getFactor($date): float
    {
        if (null !== $factor = $this->entries[$date->format($this->keyFormat)] ?? null) {
            return $factor;
        }
        throw new \Exception("No Profile Factor for " . $date->format($this->keyFormat));
    }

    private function withinMonthFactor($from, $until): float
    {
        return $this->entries[$from->format($this->keyFormat)] / $from->format('t') * $from->diff($until)->days;
    }
}
