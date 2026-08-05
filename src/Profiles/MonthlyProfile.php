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
    public static function fromElectricTemplates(): array
    {
        return self::fromTemplates(__DIR__ . '/templates/electric/monthly/');
    }

    /**
     * @return array<string, Profile>
     */
    public static function fromGasTemplates(): array
    {
        return self::fromTemplates(__DIR__ . '/templates/gas/monthly/');
    }

    /**
     * @return array<string, Profile>
     */
    public static function fromTemplates(string $path): array
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
        if ($from >= $until) {
            return 0.0;
        }

        if ($from->format('Y-m') === $until->format('Y-m')) {
            return $this->partialMonthFactor($from, $from->diff($until)->days);
        }

        $from = clone $from;
        $untilMonth = $until->format('Y-m');

        // Angebrochener Monatsstart
        $sumFactor = $this->partialMonthFactor($from, (int)$from->format('t') - (int)$from->format('j') + 1);

        // Volle Zwischenmonate
        while ($from->modify('last day of +1 month')->format('Y-m') !== $untilMonth) {
            $sumFactor += $this->getFactor($from);
        }

        // Angebrochener Monatsschluß, endet die Periode am Monatsersten entfällt er
        return $sumFactor + $this->partialMonthFactor($until, (int)$until->format('j') - 1);
    }

    public function yearlyFactor(DateTime $targetDate): float
    {
        return $this->getPeriodeFactor((clone $targetDate)->modify('-1 year'), $targetDate);
    }

    private function getFactor(DateTime $date): float
    {
        if (null !== $factor = $this->entries[$date->format($this->keyFormat)] ?? null) {
            return $factor;
        }

        throw new RuntimeException("No Profile Factor for " . (string)$date->format($this->keyFormat));
    }

    private function partialMonthFactor(DateTime $month, int $days): float
    {
        if ($days <= 0) {
            return 0.0;
        }

        return $this->getFactor($month) / (int)$month->format('t') * $days;
    }
}
