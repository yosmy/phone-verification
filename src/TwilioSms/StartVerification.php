<?php

namespace Yosmy\Phone\TwilioSms;

use Yosmy;

/**
 * @di\service()
 */
class StartVerification extends Yosmy\Phone\Manual\StartVerification
{
    /**
     * @di\arguments({
     *     appName: "%app_name%",
     * })
     *
     * @param Yosmy\Phone\Verification\ObtainAttempt $obtainAttempt
     * @param Yosmy\Phone\Verification\Attempt\IncreaseStarts $increaseStarts
     * @param Yosmy\Phone\Verification\SetCode $setCode
     * @param string $appName
     * @param Yosmy\Phone\Twilio\SendSms $sendSms
     */
    public function __construct(
        Yosmy\Phone\Verification\ObtainAttempt $obtainAttempt,
        Yosmy\Phone\Verification\Attempt\IncreaseStarts $increaseStarts,
        Yosmy\Phone\Verification\SetCode $setCode,
        string $appName,
        Yosmy\Phone\Twilio\SendSms $sendSms
    ) {
        parent::__construct(
            $obtainAttempt,
            $increaseStarts,
            $setCode,
            $appName,
            $sendSms
        );
    }
}