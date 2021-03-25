<?php

namespace Proengeno\ReadingCalculator;

function sigmoid(float $a, float $b, float $c, float $d, float $v, float $temperature, int $c9 = 40): float
{
    return (($a / (1 + pow($b / ($temperature-$c9), $c))) + $d) * $v;
}
