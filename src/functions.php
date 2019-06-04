<?php

namespace Proengeno\ReadingCalculator;

function sigmoid($a, $b, $c, $d, $v, $temperature, $c9 = 40)
{
    return (($a / (1 + pow($b / ($temperature-$c9), $c))) + $d) * $v;
}
