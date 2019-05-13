<?php

namespace Yosmy\Phone\Verification\Attempt;

use Yosmy\Phone\Verification\Attempt;
use Yosmy\Phone\Verification\ManageAttemptCollection;

/**
 * @di\service()
 */
class IncreaseCompletes
{
    /**
     * @var ManageAttemptCollection
     */
    private $manageCollection;

    /**
     * @param ManageAttemptCollection $manageCollection
     */
    public function __construct(
        ManageAttemptCollection $manageCollection
    ) {
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param string $id
     *
     * @throws ExceededCompletesException
     */
    public function increase(
        string $id
    ) {
        /** @var Attempt $attempt */
        $attempt = $this->manageCollection->findOne([
            '_id' => $id
        ]);

        if ($attempt->getCompletes() == 5) {
            throw new ExceededCompletesException();
        }

        $this->manageCollection->updateOne(
            [
                '_id' => $id
            ],
            [
                '$inc' => [
                    'completes' => 1
                ]
            ]
        );
    }
}