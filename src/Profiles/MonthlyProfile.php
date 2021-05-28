<?php

namespace Proengeno\ReadingCalculator\Profiles;

use DateTime;
use InvalidArgumentException;
use RuntimeException;

class MonthlyProfile implements Profile
{
    /** @var array<string, float> */
    protected array $entries = [];

    protected string $keyFormat;

    public function __construct(string $keyFormat = 'Y-m')
    {
        $this->keyFormat = $keyFormat;
    }

    /**
     * @return array<string, Profile>
     */
    public static function fromElectricTemplates(string $path = __DIR__ . '/templates/electric/monthly/'): array
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException("Invalid path $path");
        }

        $instances = [];
        foreach (array_slice(scandir($path), 2) as $file) {
            if (is_file($path . $file)) {
                /**
                 * @psalm-suppress UnresolvableInclude
                 * @psalm-var list<array{DateTime, float}>
                 */
                $templateProfile = include($path . $file);
                $instances[strtoupper(pathinfo($file, PATHINFO_FILENAME))] = static::fromArray($templateProfile, 'm');
            }
        }

        return $instances;
    }

    /**
     * @psalm-param list<array{DateTime, float}> $entries
     */
    public static function fromArray(array $entries, string $keyFormat = 'Y-m'): Profile
    {
        $instance = new self($keyFormat);

        foreach ($entries as [$key, $factor]) {
            $instance->addEntry($key, $factor);
        }

        return $instance;
    }

    public function addEntry(DateTime $start, float $factor): void
    {
        $this->entries[$start->format($this->keyFormat)] = $factor;
    }

    public function getPeriodeFactor(DateTime $from, DateTime $until): float
    {
        $from = clone $from;

        // Angebrochener Monatsstart
        $sumFactor = $this->withinMonthFactor($from, (clone $from)->modify('last day of this month'));

        // Volle Zwischenmonate
        while ($from->modify('last day of +1 month') <= $until) {
            $sumFactor += $this->getFactor($from);
        }

        // Angebrochener Monatsschluß
        $sumFactor += $this->withinMonthFactor($from->modify('first day of this month'), $until);

        return $sumFactor;
    }

    public function yearlyFactor(DateTime $targetDate): float
    {
        return $this->getPeriodeFactor((clone $targetDate)->modify('-1 year + 1 day'), $targetDate);
    }

    private function getFactor(DateTime $date): float
    {
        if (null !== $factor = $this->entries[$date->format($this->keyFormat)] ?? null) {
            return $factor;
        }

        throw new RuntimeException("No Profile Factor for " . (string)$date->format($this->keyFormat));
    }

    private function withinMonthFactor(DateTime $from, DateTime $until): float
    {
        return $this->getFactor($from) / (int)$from->format('t') * $from->diff($until)->days;
    }
}
