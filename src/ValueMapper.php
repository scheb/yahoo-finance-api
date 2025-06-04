<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi;

use Scheb\YahooFinanceApi\Exception\InvalidValueException;

/**
 * @final
 */
class ValueMapper implements ValueMapperInterface
{
    public function mapArray(array $rawValue, string $type): array
    {
        return array_map(
            /**
             * @param mixed $value
             *
             * @return mixed
             */
            fn ($value): float|int|\DateTimeInterface|string|bool|array|null => $this->mapValue($value, $type),
            $rawValue
        );
    }

    /**
     * @param mixed $rawValue
     */
    public function mapValue($rawValue, string $type, ?string $subType = null): float|int|\DateTimeInterface|string|bool|array|null
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
            case self::TYPE_ARRAY:
                if (null === $subType) {
                    throw new \InvalidArgumentException('Subtype must be provided for array type');
                }

                return $this->mapArray($rawValue, $subType);
            default:
                throw new \InvalidArgumentException(\sprintf('Invalid data type %s', $type));
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
        if (is_numeric($rawValue)) {
            return (bool) $rawValue;
        }

        if (!\is_bool($rawValue)) {
            throw new InvalidValueException(ValueMapperInterface::TYPE_BOOL);
        }

        return $rawValue;
    }

    private function mapDateValue(mixed $rawValue): \DateTimeInterface
    {
        try {
            return new \DateTime('@'.$rawValue);
        } catch (\Exception) {
            throw new InvalidValueException(ValueMapperInterface::TYPE_DATE);
        }
    }
}
