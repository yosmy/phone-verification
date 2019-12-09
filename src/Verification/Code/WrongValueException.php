<?php

namespace Yosmy\Phone\Verification\Code;

use Exception;
use JsonSerializable;

class WrongValueException extends Exception implements JsonSerializable
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'value' => $this->value
        ];
    }
}