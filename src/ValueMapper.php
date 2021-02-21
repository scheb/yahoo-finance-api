<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\Exception\InvalidValueException;

class ValueMapper implements ValueMapperInterface
{
    /**
     * @param mixed $rawValue
     *
     * @return mixed
     */
    public function mapValue($rawValue, string $type)
    {
        if (null === $rawValue) {
            return null;
        }

        switch ($type) {
            case self::TYPE_FLOAT:
                return $this->mapFloatValue($rawValue);
            case self::TYPE_INT:
                return $this->mapIntValue($rawValue);
            case self::TYPE_DATE:
                return $this->mapDateValue($rawValue);
            case self::TYPE_STRING:
                return (string) $rawValue;
            case self::TYPE_BOOL:
                return $this->mapBoolValue($rawValue);
            default:
                throw new \InvalidArgumentException(sprintf('Invalid data type %s', $type));
        }
    }

    /**
     * @param mixed $rawValue
     */
    private function mapFloatValue($rawValue): float
    {
        if (!is_numeric($rawValue)) {
            throw new InvalidValueException(ValueMapperInterface::TYPE_FLOAT);
        }

        return (float) $rawValue;
    }

    /**
     * @param mixed $rawValue
     */
    private function mapIntValue($rawValue): int
    {
        if (!is_numeric($rawValue)) {
            throw new InvalidValueException(ValueMapperInterface::TYPE_INT);
        }

        return (int) $rawValue;
    }

    /**
     * @param mixed $rawValue
     */
    private function mapBoolValue($rawValue): bool
    {
        return (bool) $rawValue;
    }

    /**
     * @param mixed $rawValue
     */
    private function mapDateValue($rawValue): \DateTimeInterface
    {
        try {
            return new \DateTime('@'.$rawValue);
        } catch (\Exception $e) {
            throw new InvalidValueException(ValueMapperInterface::TYPE_DATE);
        }
    }
}
