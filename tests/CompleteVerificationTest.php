<?php

namespace Yosmy\Phone\Test;

use Yosmy\Phone;
use PHPUnit\Framework\TestCase;
use LogicException;
use Yosmy\Phone\VerificationException;

class CompleteVerificationTest extends TestCase
{
    public function testComplete()
    {
        $country = 'country';
        $prefix = 'prefix';
        $number = 'number';
        $code = 'code';

        $analyzeCompleteVerificationIn = $this->createMock(Phone\AnalyzePreCompleteVerification::class);

        $analyzeCompleteVerificationIn->expects($this->once())
            ->method('analyze')
            ->with(
                $country,
                $prefix,
                $number
            );

        $executeProcess = $this->createMock(Phone\Verification\Complete\ExecuteProcess::class);

        $executeProcess->expects($this->once())
            ->method('execute')
            ->with(
                $country,
                $prefix,
                $number,
                $code
            );

        $analyzePostCompleteVerificationSuccess = $this->createMock(Phone\AnalyzePostCompleteVerificationSuccess::class);

        $analyzePostCompleteVerificationSuccess->expects($this->once())
            ->method('analyze')
            ->with(
                $country,
                $prefix,
                $number
            );

        $completeVerification = new Phone\CompleteVerification(
            $executeProcess,
            [$analyzeCompleteVerificationIn],
            [$analyzePostCompleteVerificationSuccess],
            []
        );

        try {
            $completeVerification->complete(
                $country,
                $prefix,
                $number,
                $code
            );
        } catch (Phone\VerificationException $e) {
            throw new LogicException();
        }
    }

    /**
     * @throws VerificationException
     */
    public function testHavingVerificationExceptionOnAnalyzeIn()
    {
        $country = 'country';
        $prefix = 'prefix';
        $number = 'number';
        $code = 'code';

        $exception = new Phone\VerificationException('message');

        $analyzeCompleteVerificationIn = $this->createMock(Phone\AnalyzePreCompleteVerification::class);

        $analyzeCompleteVerificationIn->expects($this->once())
            ->method('analyze')
            ->willThrowException($exception);

        $executeProcess = $this->createMock(Phone\Verification\Complete\ExecuteProcess::class);

        $analyzePostCompleteVerificationFail = $this->createMock(Phone\AnalyzePostCompleteVerificationFail::class);

        $analyzePostCompleteVerificationFail->expects($this->once())
            ->method('analyze')
            ->with(
                $country,
                $prefix,
                $number,
                $exception
            );

        $this->expectExceptionObject($exception);

        $completeVerification = new Phone\CompleteVerification(
            $executeProcess,
            [$analyzeCompleteVerificationIn],
            [],
            [$analyzePostCompleteVerificationFail]
        );

        try {
            $completeVerification->complete(
                $country,
                $prefix,
                $number,
                $code
            );
        } catch (VerificationException $e) {
            throw $e;
        }
    }

    /**
     * @throws VerificationException
     */
    public function testCompleteHavingVerificationExceptionOnExecuteProcess()
    {
        $country = 'country';
        $prefix = 'prefix';
        $number = 'number';
        $code = 'code';

        $exception = new Phone\VerificationException('message');

        $executeProcess = $this->createMock(Phone\Verification\Complete\ExecuteProcess::class);

        $executeProcess->expects($this->once())
            ->method('execute')
            ->willThrowException($exception);

        $analyzePostCompleteVerificationFail = $this->createMock(Phone\AnalyzePostCompleteVerificationFail::class);

        $analyzePostCompleteVerificationFail->expects($this->once())
            ->method('analyze')
            ->with(
                $country,
                $prefix,
                $number,
                $exception
            );

        $this->expectExceptionObject($exception);

        $completeVerification = new Phone\CompleteVerification(
            $executeProcess,
            [],
            [],
            [$analyzePostCompleteVerificationFail]
        );

        try {
            $completeVerification->complete(
                $country,
                $prefix,
                $number,
                $code
            );
        } catch (VerificationException $e) {
            throw $e;
        }
    }
}