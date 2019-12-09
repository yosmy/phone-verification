<?php

namespace Yosmy\Phone\Manual;

use Yosmy;

/**
 * @di\service()
 */
class CompleteVerification implements Yosmy\Phone\CompleteVerification
{
    /**
     * @var Yosmy\Phone\Verification\ObtainAttempt
     */
    private $obtainAttempt;

    /**
     * @var Yosmy\Phone\Verification\Attempt\IncreaseCompletes
     */
    private $increaseCompletes;

    /**
     * @var Yosmy\Phone\Verification\AssertCode
     */
    private $assertCode;

    /**
     * @var Yosmy\Phone\Verification\ResetAttempt
     */
    private $resetAttempt;

    /**
     * @param Yosmy\Phone\Verification\ObtainAttempt             $obtainAttempt
     * @param Yosmy\Phone\Verification\Attempt\IncreaseCompletes $increaseCompletes
     * @param Yosmy\Phone\Verification\AssertCode                $assertCode
     * @param Yosmy\Phone\Verification\ResetAttempt              $resetAttempt
     */
    public function __construct(
        Yosmy\Phone\Verification\ObtainAttempt $obtainAttempt,
        Yosmy\Phone\Verification\Attempt\IncreaseCompletes $increaseCompletes,
        Yosmy\Phone\Verification\AssertCode $assertCode,
        Yosmy\Phone\Verification\ResetAttempt $resetAttempt
    ) {
        $this->obtainAttempt = $obtainAttempt;
        $this->increaseCompletes = $increaseCompletes;
        $this->assertCode = $assertCode;
        $this->resetAttempt = $resetAttempt;
    }

    /**
     * {@inheritDoc}
     */
    public function complete(
        string $prefix,
        string $number,
        int $code
    ) {
        $attempt = $this->obtainAttempt->obtain(
            $prefix,
            $number
        );

        try {
            $this->increaseCompletes->increase(
                $attempt->getId()
            );
        } catch (Yosmy\Phone\Verification\Attempt\ExceededCompletesException $e) {
            throw $e;
        }

        if (!$this->assertCode->assert(
            $prefix,
            $number,
            $code
        )) {
            throw new Yosmy\Phone\Verification\Code\WrongValueException();
        }

        $this->resetAttempt->reset(
            $prefix,
            $number
        );
    }
}