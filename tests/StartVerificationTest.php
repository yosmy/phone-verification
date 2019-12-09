<?php

namespace Yosmy\Phone\Test;

use Yosmy\Phone;
use PHPUnit\Framework\TestCase;
use LogicException;
use Yosmy\Phone\VerificationException;

class StartVerificationTest extends TestCase
{
    public function testStart()
    {
        $country = 'country';
        $prefix = 'prefix';
        $number = 'number';
        $template = 'template %s';

        $analyzePreStartVerification = $this->createMock(Phone\AnalyzePreStartVerification::class);

        $analyzePreStartVerification->expects($this->once())
            ->method('analyze')
            ->with(
                $country,
                $prefix,
                $number
            );

        $executeProcess = $this->createMock(Phone\Verification\Start\ExecuteProcess::class);

        $executeProcess->expects($this->once())
            ->method('execute')
            ->with(
                $country,
                $prefix,
                $number,
                $template
            );

        $analyzePostStartVerificationSuccess = $this->createMock(Phone\AnalyzePostStartVerificationSuccess::class);

        $analyzePostStartVerificationSuccess->expects($this->once())
            ->method('analyze')
            ->with(
                $country,
                $prefix,
                $number
            );

        $startVerification = new Phone\StartVerification(
            $executeProcess,
            [$analyzePreStartVerification],
            [$analyzePostStartVerificationSuccess],
            []
        );

        try {
            $startVerification->start(
                $country,
                $prefix,
                $number,
                $template
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
        $template = 'template %s';

        $exception = new Phone\VerificationException('message');

        $analyzePreStartVerification = $this->createMock(Phone\AnalyzePreStartVerification::class);

        $analyzePreStartVerification->expects($this->once())
            ->method('analyze')
            ->willThrowException($exception);

        $executeProcess = $this->createMock(Phone\Verification\Start\ExecuteProcess::class);

        $analyzePostStartVerificationFail = $this->createMock(Phone\AnalyzePostStartVerificationFail::class);

        $analyzePostStartVerificationFail->expects($this->once())
            ->method('analyze')
            ->with(
                $country,
                $prefix,
                $number,
                $exception
            );

        $this->expectExceptionObject($exception);

        $startVerification = new Phone\StartVerification(
            $executeProcess,
            [$analyzePreStartVerification],
            [],
            [$analyzePostStartVerificationFail]
        );

        try {
            $startVerification->start(
                $country,
                $prefix,
                $number,
                $template
            );
        } catch (VerificationException $e) {
            throw $e;
        }
    }

    /**
     * @throws VerificationException
     */
    public function testStartHavingVerificationExceptionOnExecuteProcess()
    {
        $country = 'country';
        $prefix = 'prefix';
        $number = 'number';
        $template = 'template %s';

        $exception = new Phone\VerificationException('message');

        $executeProcess = $this->createMock(Phone\Verification\Start\ExecuteProcess::class);

        $executeProcess->expects($this->once())
            ->method('execute')
            ->willThrowException($exception);

        $analyzePostStartVerificationFail = $this->createMock(Phone\AnalyzePostStartVerificationFail::class);

        $analyzePostStartVerificationFail->expects($this->once())
            ->method('analyze')
            ->with(
                $country,
                $prefix,
                $number,
                $exception
            );

        $this->expectExceptionObject($exception);

        $startVerification = new Phone\StartVerification(
            $executeProcess,
            [],
            [],
            [$analyzePostStartVerificationFail]
        );

        try {
            $startVerification->start(
                $country,
                $prefix,
                $number,
                $template
            );
        } catch (VerificationException $e) {
            throw $e;
        }
    }
}