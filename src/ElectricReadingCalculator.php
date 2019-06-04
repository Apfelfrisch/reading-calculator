<?php

namespace Proengeno\ReadingCalculator;

use DateTime;
use Proengeno\ReadingCalculator\Profiles\Profile;
use Proengeno\ReadingCalculator\Profiles\MonthlyProfile;

class ElectricReadingCalculator extends ReadingCalculator
{
    public static function withProfileTemplates($profileType, $fallBackProfile = null)
    {
        switch ($profileType) {
            case 'monthly':
                $instance = new static;
                foreach (MonthlyProfile::fromElectricTemplates() as $key => $profile) {
                    $instance->addProfile($key, $profile, $fallBackProfile == $key);
                }
                return $instance;
        }
        throw new \InvalidArgumentException("Unknow profile Type $profileType");
    }
}
