<?php

namespace Yosmy\Phone\Verification;

/**
 * @di\service()
 */
class AssertCode
{
    /**
     * @var ManageCodeCollection
     */
    private $manageCollection;

    /**
     * @param ManageCodeCollection $manageCollection
     */
    public function __construct(
        ManageCodeCollection $manageCollection
    ) {
        $this->manageCollection = $manageCollection;
    }

    /**
     * @param string $prefix
     * @param string $number
     * @param int $value
     *
     * @return bool
     */
    public function assert(
        string $prefix,
        string $number,
        int $value
    ) {
        /** @var Code $code */
        $code = $this->manageCollection->findOne(
            [
                'prefix' => $prefix,
                'number' => $number,
            ]
        );

        return $code->getValue() == $value;
    }
}