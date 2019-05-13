<?php

namespace Yosmy\Phone\Verification;

use MongoDB\Model\BSONDocument;

class Attempt extends BSONDocument
{
    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->offsetGet('id');
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->offsetGet('country');
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->offsetGet('prefix');
    }


    /**
     * @return string
     */
    public function getStarts(): string
    {
        return $this->offsetGet('starts');
    }

    /**
     * @return string
     */
    public function getCompletes(): string
    {
        return $this->offsetGet('completes');
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $data['id'] = $data['_id'];
        unset($data['_id']);

        parent::bsonUnserialize($data);
    }
}