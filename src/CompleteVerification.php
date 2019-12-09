<?php

namespace Yosmy\Phone;

use Yosmy;

/**
 * @di\service()
 */
class CompleteVerification
{
    /**
     * @var Verification\Complete\ExecuteProcess
     */
    private $executeProcess;

    /**
     * @var AnalyzePreCompleteVerification[]
     */
    private $analyzePreCompleteVerificationServices;

    /**
     * @var AnalyzePostCompleteVerificationSuccess[]
     */
    private $analyzePostCompleteVerificationSuccessServices;

    /**
     * @var AnalyzePostCompleteVerificationFail[]
     */
    private $analyzePostCompleteVerificationFailServices;

    /**
     * @di\arguments({
     *     analyzePreCompleteVerificationServices:         '#yosmy.phone.pre_complete_verification',
     *     analyzePostCompleteVerificationSuccessServices: '#yosmy.phone.post_complete_verification_success',
     *     analyzePostCompleteVerificationFailServices:    '#yosmy.phone.post_complete_verification_fail'
     * })
     * 
     * @param Verification\Complete\ExecuteProcess     $executeProcess
     * @param AnalyzePreCompleteVerification[]         $analyzePreCompleteVerificationServices
     * @param AnalyzePostCompleteVerificationSuccess[] $analyzePostCompleteVerificationSuccessServices
     * @param AnalyzePostCompleteVerificationFail[]    $analyzePostCompleteVerificationFailServices
     */
    public function __construct(
        Verification\Complete\ExecuteProcess $executeProcess, 
        array $analyzePreCompleteVerificationServices, 
        array $analyzePostCompleteVerificationSuccessServices, 
        array $analyzePostCompleteVerificationFailServices
    ) {
        $this->executeProcess = $executeProcess;
        $this->analyzePreCompleteVerificationServices = $analyzePreCompleteVerificationServices;
        $this->analyzePostCompleteVerificationSuccessServices = $analyzePostCompleteVerificationSuccessServices;
        $this->analyzePostCompleteVerificationFailServices = $analyzePostCompleteVerificationFailServices;
    }

    /**
     * @param string $country
     * @param string $prefix
     * @param string $number
     * @param string $code
     *
     * @throws VerificationException
     */
    public function complete(
        string $country,
        string $prefix,
        string $number,
        string $code
    ) {
        foreach ($this->analyzePreCompleteVerificationServices as $analyzePreCompleteVerification) {
            try {
                $analyzePreCompleteVerification->analyze(
                    $country,
                    $prefix,
                    $number
                );
            } catch (VerificationException $e) {
                foreach ($this->analyzePostCompleteVerificationFailServices as $analyzePostCompleteVerificationFail) {
                    $analyzePostCompleteVerificationFail->analyze(
                        $country,
                        $prefix,
                        $number,
                        $e
                    );
                }

                throw $e;
            }
        }

        try {
            $this->executeProcess->execute(
                $country,
                $prefix,
                $number,
                $code
            );
        } catch (VerificationException $e) {
            foreach ($this->analyzePostCompleteVerificationFailServices as $analyzePostCompleteVerificationFail) {
                $analyzePostCompleteVerificationFail->analyze(
                    $country,
                    $prefix,
                    $number,
                    $e
                );
            }

            throw $e;
        }

        foreach ($this->analyzePostCompleteVerificationSuccessServices as $analyzePostCompleteVerificationSuccess) {
            $analyzePostCompleteVerificationSuccess->analyze(
                $country,
                $prefix,
                $number
            );
        }
    }
}