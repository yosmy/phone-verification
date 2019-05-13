<?php

namespace Yosmy\Phone\Test\Twilio;

use Yosmy\Phone\TwilioVerify;

/**
 * @di\service()
 */
class StartVerification
{
    /**
     * @var TwilioVerify\StartVerification
     */
    private $startVerification;

    /**
     * @param TwilioVerify\StartVerification $startVerification
     */
    public function __construct(TwilioVerify\StartVerification $startVerification)
    {
        $this->startVerification = $startVerification;
    }

    /**
     * @cli\resolution({command: "/start"})
     */
    public function start()
    {
    }
}
