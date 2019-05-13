<?php

namespace Yosmy\Phone\Verification\Attempt;

use Yosmy\Phone\Verification\Attempt;
use Yosmy\Phone\Verification\ManageAttemptCollection;

/**
 * @di\service()
 */
class IncreaseStarts
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
     * @throws ExceededStartsException
     */
    public function increase(
        string $id
    ) {
        /** @var Attempt $attempt */
        $attempt = $this->manageCollection->findOne([
            '_id' => $id
        ]);

        if ($attempt->getStarts() == 3) {
            throw new ExceededStartsException();
        }

        $this->manageCollection->updateOne(
            [
                '_id' => $id
            ],
            [
                '$inc' => [
                    'starts' => 1
                ]
            ]
        );
    }
}