<?php

namespace Yosmy\Phone;

use Yosmy;

interface AnalyzePostCompleteVerificationSuccess
{
    /**
     * @param string $country
     * @param string $prefix
     * @param string $number
     *
     * @throws VerificationException
     */
    public function analyze(
        string $country,
        string $prefix,
        string $number
    );
}