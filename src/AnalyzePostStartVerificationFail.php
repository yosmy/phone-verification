<?php

namespace Yosmy\Phone;

use Yosmy;

interface AnalyzePostStartVerificationFail
{
    /**
     * @param string    $country
     * @param string    $prefix
     * @param string    $number
     * @param VerificationException $e
     */
    public function analyze(
        string $country,
        string $prefix,
        string $number,
        VerificationException $e
    );
}