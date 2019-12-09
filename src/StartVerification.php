<?php

namespace Yosmy\Phone;

use Yosmy;

/**
 * @di\service()
 */
class StartVerification
{
    /**
     * @var Verification\Start\ExecuteProcess
     */
    private $executeProcess;

    /**
     * @var AnalyzePreStartVerification[]
     */
    private $analyzePreStartVerificationServices;

    /**
     * @var AnalyzePostStartVerificationSuccess[]
     */
    private $analyzePostStartVerificationSuccessServices;

    /**
     * @var AnalyzePostStartVerificationFail[]
     */
    private $analyzePostStartVerificationFailServices;

    /**
     * @di\arguments({
     *     analyzePreStartVerificationServices:         '#yosmy.phone.pre_start_verification',
     *     analyzePostStartVerificationSuccessServices: '#yosmy.phone.post_start_verification_success',
     *     analyzePostStartVerificationFailServices:    '#yosmy.phone.post_start_verification_fail'
     * })
     * 
     * @param Verification\Start\ExecuteProcess     $executeProcess
     * @param AnalyzePreStartVerification[]         $analyzePreStartVerificationServices
     * @param AnalyzePostStartVerificationSuccess[] $analyzePostStartVerificationSuccessServices
     * @param AnalyzePostStartVerificationFail[]    $analyzePostStartVerificationFailServices
     */
    public function __construct(
        Verification\Start\ExecuteProcess $executeProcess, 
        array $analyzePreStartVerificationServices,
        array $analyzePostStartVerificationSuccessServices,
        array $analyzePostStartVerificationFailServices
    ) {
        $this->executeProcess = $executeProcess;
        $this->analyzePreStartVerificationServices = $analyzePreStartVerificationServices;
        $this->analyzePostStartVerificationSuccessServices = $analyzePostStartVerificationSuccessServices;
        $this->analyzePostStartVerificationFailServices = $analyzePostStartVerificationFailServices;
    }

    /**
     * @param string $country
     * @param string $prefix
     * @param string $number
     * @param string $template
     *
     * @throws VerificationException
     */
    public function start(
        string $country,
        string $prefix,
        string $number,
        string $template
    ) {
        foreach ($this->analyzePreStartVerificationServices as $analyzePreStartVerification) {
            try {
                $analyzePreStartVerification->analyze(
                    $country,
                    $prefix,
                    $number
                );
            } catch (VerificationException $e) {
                foreach ($this->analyzePostStartVerificationFailServices as $analyzePostStartVerificationFail) {
                    $analyzePostStartVerificationFail->analyze(
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
                $template
            );
        } catch (VerificationException $e) {
            foreach ($this->analyzePostStartVerificationFailServices as $analyzePostStartVerificationFail) {
                $analyzePostStartVerificationFail->analyze(
                    $country,
                    $prefix,
                    $number,
                    $e
                );
            }

            throw $e;
        }

        foreach ($this->analyzePostStartVerificationSuccessServices as $analyzePostStartVerificationSuccess) {
            $analyzePostStartVerificationSuccess->analyze(
                $country,
                $prefix,
                $number
            );
        }
    }
}