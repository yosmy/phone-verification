<?php

namespace Yosmy\Phone\Verification;

use Yosmy\Mongo\DuplicatedKeyException;
use LogicException;

/**
 * @di\service()
 */
class SetCode
{
    /**
     * @var GenerateCode
     */
    private $generateCode;

    /**
     * @var ManageCodeCollection
     */
    private $manageCollection;

    /**
     * @param GenerateCode         $generateCode
     * @param ManageCodeCollection $manageCollection
     */
    public function __construct(
        GenerateCode $generateCode,
        ManageCodeCollection $manageCollection
    ) {
        $this->generateCode = $generateCode;
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param string $prefix
     * @param string $number
     *
     * @return string
     */
    public function set(
        string $prefix,
        string $number
    ) {
        /** @var Code $code */
        $code = $this->manageCollection->findOne([
            'prefix' => $prefix,
            'number' => $number,
        ]);

        if ($code) {
            // Just keep sending the same code
            return $code->getValue();
        }

        $value = $this->generateCode->generate(6);

        try {
            $this->manageCollection->insertOne([
                '_id' => uniqid(),
                'prefix' => $prefix,
                'number' => $number,
                'value' => $value,
            ]);
        } catch (DuplicatedKeyException $e) {
            throw new LogicException(null, null, $e);
        }

        return $value;
    }
}