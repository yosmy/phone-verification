<?php

namespace Yosmy\Phone\Verification;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

/**
 * @di\service({
 *     private: true
 * })
 */
class GenerateCode
{
    /**
     * @param int $length
     *
     * @return int
     */
    public function generate(int $length)
    {
        $generator = new ComputerPasswordGenerator();

        $generator
            ->setLowercase(false)
            ->setUppercase(false)
            ->setSymbols(false)
            ->setLength($length);

        return (int) $generator->generatePassword();
    }
}