<?php

namespace Yosmy\Phone;

use Yosmy;

interface CompleteVerification
{
    /**
     * @param string $prefix
     * @param string $number
     * @param int    $code
     *
     * @throws Yosmy\Phone\Verification\Attempt\ExceededCompletesException
     * @throws Yosmy\Phone\Verification\Code\WrongValueException
     */
    public function complete(
        string $prefix,
        string $number,
        int $code
    );
}