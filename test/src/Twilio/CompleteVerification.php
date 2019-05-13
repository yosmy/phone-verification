<?php

namespace Yosmy\Phone\Test\Twilio;

use Yosmy\Phone\TwilioVerify;

/**
 * @di\service()
 */
class CompleteVerification
{
    /**
     * @var TwilioVerify\CompleteVerification
     */
    private $completeVerification;

    /**
     * @param TwilioVerify\CompleteVerification $completeVerification
     */
    public function __construct(TwilioVerify\CompleteVerification $completeVerification)
    {
        $this->completeVerification = $completeVerification;
    }

    /**
     * @cli\resolution({command: "/complete"})
     */
    public function complete()
    {

    }
}
