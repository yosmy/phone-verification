<?php

namespace Yosmy\Phone\TwilioVerify;

use Yosmy;

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
     * @var string
     */
    private $accountSID;

    /**
     * @var string
     */
    private $authToken;

    /**
     * @var string
     */
    private $serviceSid;

    /**
     * @var Yosmy\Http\ExecuteRequest
     */
    private $executeRequest;

    /**
     * @di\arguments({
     *     accountSID: "%twilio_account_sid%",
     *     authToken:  "%twilio_auth_token%",
     *     serviceSid: "%twilio_verify_service_sid%",
     * })
     *
     * @param Yosmy\Phone\Verification\ObtainAttempt $obtainAttempt
     * @param Yosmy\Phone\Verification\Attempt\IncreaseStarts $increaseStarts
     * @param string $accountSID
     * @param string $authToken
     * @param string $serviceSid
     * @param Yosmy\Http\ExecuteRequest $executeRequest
     */
    public function __construct(
        Yosmy\Phone\Verification\ObtainAttempt $obtainAttempt,
        Yosmy\Phone\Verification\Attempt\IncreaseStarts $increaseStarts,
        string $accountSID,
        string $authToken,
        string $serviceSid,
        Yosmy\Http\ExecuteRequest $executeRequest
    ) {
        $this->obtainAttempt = $obtainAttempt;
        $this->increaseStarts = $increaseStarts;
        $this->accountSID = $accountSID;
        $this->authToken = $authToken;
        $this->serviceSid = $serviceSid;
        $this->executeRequest = $executeRequest;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Yosmy\Http\Exception
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

        try {
            $this->executeRequest->execute(
                'POST',
                sprintf('https://verify.twilio.com/v2/Services/%s/Verifications', $this->serviceSid),
                [
                    'auth' => [$this->accountSID, $this->authToken],
                    'form_params' => [
                        'To' => sprintf('+%s%s', $prefix, $number),
                        'Channel' => 'sms',
                        'Locale' => 'es'
                    ]
                ]
            );
        } catch (Yosmy\Http\Exception $e) {
            throw $e;
        }
    }
}