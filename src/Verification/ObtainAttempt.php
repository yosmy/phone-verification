<?php

namespace Yosmy\Phone\Verification;

use Yosmy\Mongo\DuplicatedKeyException;
use LogicException;

/**
 * @di\service()
 */
class ObtainAttempt
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
     * @param string $prefix
     * @param string $number
     *
     * @return Attempt
     */
    public function obtain(
        string $prefix,
        string $number
    ) {
        /** @var Attempt $attempt */
        $attempt = $this->manageCollection->findOne([
            'prefix' => $prefix,
            'number' => $number,
        ]);

        if (!$attempt) {
            try {
                $this->manageCollection->insertOne([
                    '_id' => uniqid(),
                    'prefix' => $prefix,
                    'number' => $number,
                    'starts' => 0,
                    'completes' => 0
                ]);
            } catch (DuplicatedKeyException $e) {
                throw new LogicException(null, null, $e);
            }

            return $this->obtain(
                $prefix,
                $number
            );
        }

        return $attempt;
    }
}