<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Scheb\YahooFinanceApi\Exception\InvalidValueException;
use Scheb\YahooFinanceApi\ValueMapper;
use Scheb\YahooFinanceApi\ValueMapperInterface;

class ValueMapperTest extends TestCase
{
    private ValueMapper $valueMapper;

    protected function setUp(): void
    {
        $this->valueMapper = new ValueMapper();
    }

    #[Test]
    public function mapValue_invalidType_throwInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->valueMapper->mapValue('invalid', 'value');
    }

    #[Test]
    #[DataProvider('provideValidValues')]
    public function mapValue_passValidValue_returnMappedValue(string $type, string|float|int $inputValue, float|int|string|\DateTime|bool $expectedOutput): void
    {
        $returnValue = $this->valueMapper->mapValue($inputValue, $type);
        if (\is_object($expectedOutput)) {
            $this->assertEquals($expectedOutput, $returnValue);
        } else {
            $this->assertSame($expectedOutput, $returnValue);
        }
    }

    public static function provideValidValues(): array
    {
        return [
            [ValueMapperInterface::TYPE_FLOAT, '1.123', 1.123],
            [ValueMapperInterface::TYPE_FLOAT, '1', 1.0],
            [ValueMapperInterface::TYPE_INT, '42', 42],
            [ValueMapperInterface::TYPE_INT, 42.5, 42],
            [ValueMapperInterface::TYPE_STRING, 123, '123'],
            [ValueMapperInterface::TYPE_STRING, 1.123, '1.123'],
            [ValueMapperInterface::TYPE_DATE, '1577880000', new \DateTime('2020-01-01 12:00:00+0000')],
            [ValueMapperInterface::TYPE_BOOL, '1', true],
            [ValueMapperInterface::TYPE_BOOL, '0', false],
        ];
    }

    #[Test]
    #[DataProvider('provideTypes')]
    public function mapValue_nullPassed_returnsNUll(string $type): void
    {
        $returnValue = $this->valueMapper->mapValue(null, $type);
        $this->assertNull($returnValue);
    }

    public static function provideTypes(): array
    {
        return [
            [ValueMapperInterface::TYPE_FLOAT],
            [ValueMapperInterface::TYPE_INT],
            [ValueMapperInterface::TYPE_BOOL],
            [ValueMapperInterface::TYPE_STRING],
            [ValueMapperInterface::TYPE_DATE],
        ];
    }

    #[Test]
    #[DataProvider('provideInvalidValues')]
    public function mapValue_passInvalidValue_throwInvalidValueException(string $type, string $inputValue): void
    {
        $this->expectException(InvalidValueException::class);
        $this->valueMapper->mapValue($inputValue, $type);
    }

    public static function provideInvalidValues(): array
    {
        return [
            [ValueMapperInterface::TYPE_FLOAT, ''],
            [ValueMapperInterface::TYPE_FLOAT, 'invalid'],
            [ValueMapperInterface::TYPE_INT, ''],
            [ValueMapperInterface::TYPE_INT, 'invalid'],
            [ValueMapperInterface::TYPE_DATE, 'invalid'],
        ];
    }
}
