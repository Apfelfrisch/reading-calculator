<?php

namespace Proengeno\ReadingCalculator;

use DateTime;
use Proengeno\ReadingCalculator\Profiles\Profile;

class ReadingCalculator
{
    protected $profiles;
    protected $fallbackProfile;

    public function addProfile($name, Profile $profile, $isFallback = false)
    {
        if ($isFallback) {
            $this->fallbackProfile = $name;
        }

        return $this->profiles[$name] = $profile;
    }

    public function hasProfile($name): bool
    {
        return isset($this->profiles[$name]);
    }

    public function getFallbackName(): string
    {
        return $this->fallbackProfile;
    }

    public function getYearlyUsage(string $profile, DateTime $from, DateTime $until, int $usage): float
    {
        $profile = $this->getProfile($profile);

        return $usage / $profile->getPeriodeFactor($from, $until) * $profile->yearlyFactor($until);
    }

    public function getPeriodUsage(string $profile, DateTime $from, DateTime $until, int $yearlyUsage): float
    {
        $profile = $this->getProfile($profile);

        return $yearlyUsage / $profile->yearlyFactor($until) * $profile->getPeriodeFactor($from, $until);
    }

    public function getProfile($profile): Profile
    {
        if ($this->hasProfile($profile)) {
            return $this->profiles[$profile];
        }
        if ($this->hasProfile($this->getFallbackName())) {
            return $this->profiles[$this->getFallbackName()];
        }

        throw new \InvalidArgumentException("Unknow profile $profile");
    }
}
