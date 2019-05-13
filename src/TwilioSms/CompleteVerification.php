<?php

namespace Yosmy\Phone\TwilioSms;

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
     * @param Yosmy\Phone\Verification\Attempt\IncreaseCompletes $increaseCompletes
     * @param string $accountSID
     * @param string $authToken
     * @param string $serviceSid
     * @param Yosmy\Http\ExecuteRequest $executeRequest
     */
    public function __construct(
        Yosmy\Phone\Verification\ObtainAttempt $obtainAttempt,
        Yosmy\Phone\Verification\Attempt\IncreaseCompletes $increaseCompletes,
        string $accountSID,
        string $authToken,
        string $serviceSid,
        Yosmy\Http\ExecuteRequest $executeRequest
    ) {
        $this->obtainAttempt = $obtainAttempt;
        $this->increaseCompletes = $increaseCompletes;
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

        try {
            $response = $this->executeRequest->execute(
                'POST',
                sprintf('https://verify.twilio.com/v2/Services/%s/VerificationCheck', $this->serviceSid),
                [
                    'auth' => [$this->accountSID, $this->authToken],
                    'form_params' => [
                        'To' => sprintf('+%s%s', $prefix, $number),
                        'Code' => $code
                    ]
                ]
            );
        } catch (Yosmy\Http\Exception $e) {
            $response = $e->getResponse();

            if (in_array($response['code'], [20404, 60200])) {
                throw new Yosmy\Phone\Verification\Code\WrongValueException();
            }

            throw $e;
        }

        $response = $response->getBody();

        if ($response['valid'] == false) {
            throw new Yosmy\Phone\Verification\Code\WrongValueException();
        }
    }
}