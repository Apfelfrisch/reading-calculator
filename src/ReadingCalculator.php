<?php

namespace Proengeno\ReadingCalculator;

use DateTime;
use Proengeno\ReadingCalculator\Profiles\Profile;

class ReadingCalculator
{
    /** @var array<string, Profile> */
    protected array $profiles = [];

    protected ?string $fallbackProfile = null;

    public function addProfile(string $name, Profile $profile, bool $isFallback = false): void
    {
        if ($this->fallbackProfile === null || $isFallback) {
            $this->fallbackProfile = $name;
        }

        $this->profiles[$name] = $profile;
    }

    public function hasProfile(?string $name): bool
    {
        if ($name === null) {
            return false;
        }

        return isset($this->profiles[$name]);
    }

    public function getFallbackName(): ?string
    {
        return $this->fallbackProfile;
    }

    public function getYearlyUsage(string $profile, DateTime $from, DateTime $until, int|float $usage): float
    {
        $profile = $this->getProfile($profile);

        return $usage / $profile->getPeriodeFactor($from, $until) * $profile->yearlyFactor($until);
    }

    public function getPeriodUsage(string $profile, DateTime $from, DateTime $until, int|float $yearlyUsage): float
    {
        $profile = $this->getProfile($profile);

        return $yearlyUsage / $profile->yearlyFactor($until) * $profile->getPeriodeFactor($from, $until);
    }

    public function getProfile(string $profile): Profile
    {
        if ($this->hasProfile($profile)) {
            return $this->profiles[$profile];
        }
        if ($this->hasProfile($this->getFallbackName())) {
            /** @psalm-suppress PossiblyNullArrayOffset */
            return $this->profiles[$this->getFallbackName()];
        }

        throw new \InvalidArgumentException("Unknow profile $profile");
    }
}
