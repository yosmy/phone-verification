<?php

namespace Yosmy\Phone\Manual;

use Yosmy;
use LogicException;

/**
 * @di\service()
 */
class StartVerification implements Yosmy\Phone\StartVerification
{
    /**
     * @var Yosmy\Phone\Verification\ObtainAttempt
     */
    private $obtainAttempt;

    /**
     * @var Yosmy\Phone\Verification\Attempt\IncreaseStarts
     */
    private $increaseStarts;

    /**
     * @var Yosmy\Phone\Verification\SetCode
     */
    private $setCode;

    /**
     * @var string
     */
    private $appName;

    /**
     * @var Yosmy\Phone\SendSms
     */
    private $sendSms;

    /**
     * @di\arguments({
     *     appName: "%app_name%",
     * })
     *
     * @param Yosmy\Phone\Verification\ObtainAttempt $obtainAttempt
     * @param Yosmy\Phone\Verification\Attempt\IncreaseStarts $increaseStarts
     * @param Yosmy\Phone\Verification\SetCode $setCode
     * @param string $appName
     * @param Yosmy\Phone\SendSms $sendSms
     */
    public function __construct(
        Yosmy\Phone\Verification\ObtainAttempt $obtainAttempt,
        Yosmy\Phone\Verification\Attempt\IncreaseStarts $increaseStarts,
        Yosmy\Phone\Verification\SetCode $setCode,
        string $appName,
        Yosmy\Phone\SendSms $sendSms
    ) {
        $this->obtainAttempt = $obtainAttempt;
        $this->increaseStarts = $increaseStarts;
        $this->setCode = $setCode;
        $this->appName = $appName;
        $this->sendSms = $sendSms;
    }

    /**
     * {@inheritDoc}
     */
    public function start(
        string $prefix,
        string $number
    ) {
        $attempt = $this->obtainAttempt->obtain(
            $prefix,
            $number
        );

        try {
            $this->increaseStarts->increase(
                $attempt->getId()
            );
        } catch (Yosmy\Phone\Verification\Attempt\ExceededStartsException $e) {
            throw $e;
        }

        $code = $this->setCode->set(
            $prefix,
            $number
        );

        try {
            $this->sendSms->send(
                $prefix,
                $number,
                sprintf('Su codigo de verificacion para %s es: %s', $this->appName, $code)
            );
        } catch (Yosmy\Phone\SmsException $e) {
            throw new LogicException(null, null, $e);
        }
    }
}