<?php

declare(strict_types=1);

namespace Elminson\YahooFinanceApi\Tests;

use PHPUnit\Framework\TestCase;
use Elminson\YahooFinanceApi\Exception\InvalidValueException;
use Elminson\YahooFinanceApi\ValueMapper;
use Elminson\YahooFinanceApi\ValueMapperInterface;

class ValueMapperTest extends TestCase
{
    /**
     * @var ValueMapper
     */
    private $valueMapper;

    protected function setUp(): void
    {
        $this->valueMapper = new ValueMapper();
    }

    /**
     * @test
     */
    public function mapValue_invalidType_throwInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->valueMapper->mapValue('invalid', 'value');
    }

    /**
     * @test
     * @dataProvider provideValidValues
     */
    public function mapValue_passValidValue_returnMappedValue(string $type, $inputValue, $expectedOutput): void
    {
        $returnValue = $this->valueMapper->mapValue($inputValue, $type);
        if (\is_object($expectedOutput)) {
            $this->assertEquals($expectedOutput, $returnValue);
        } else {
            $this->assertSame($expectedOutput, $returnValue);
        }
    }

    public function provideValidValues(): array
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

    /**
     * @test
     * @dataProvider provideTypes
     */
    public function mapValue_nullPassed_returnsNUll(string $type): void
    {
        $returnValue = $this->valueMapper->mapValue(null, $type);
        $this->assertNull($returnValue);
    }

    public function provideTypes(): array
    {
        return [
            [ValueMapperInterface::TYPE_FLOAT],
            [ValueMapperInterface::TYPE_INT],
            [ValueMapperInterface::TYPE_BOOL],
            [ValueMapperInterface::TYPE_STRING],
            [ValueMapperInterface::TYPE_DATE],
        ];
    }

    /**
     * @test
     * @dataProvider provideInvalidValues
     */
    public function mapValue_passInvalidValue_throwInvalidValueException(string $type, $inputValue): void
    {
        $this->expectException(InvalidValueException::class);
        $this->valueMapper->mapValue($inputValue, $type);
    }

    public function provideInvalidValues(): array
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
