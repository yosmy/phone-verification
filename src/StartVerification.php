<?php

namespace Yosmy\Phone;

use Yosmy;

interface StartVerification
{
    /**
     * @param string $prefix
     * @param string $number
     *
     * @throws Yosmy\Phone\Verification\Attempt\ExceededStartsException
     */
    public function start(
        string $prefix,
        string $number
    );
}