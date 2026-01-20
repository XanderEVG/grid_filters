<?php

namespace Core;

use PHPUnit\Framework\TestCase;
use Xanderevg\GridFiltersLibrary\Core\Exceptions\FilterOperatorException;
use Xanderevg\GridFiltersLibrary\Core\OperatorMapper;

class OperatorMapperTest extends TestCase
{
    public function testResolveOperatorEq(): void
    {
        $this->assertEquals('=', OperatorMapper::resolve('eq'));
        $this->assertEquals('=', OperatorMapper::resolve('='));
    }

    public function testResolveOperatorNeq(): void
    {
        $this->assertEquals('<>', OperatorMapper::resolve('neq'));
        $this->assertEquals('<>', OperatorMapper::resolve('<>'));
    }

    public function testResolveInvaidOperator(): void
    {
        $this->expectException(FilterOperatorException::class);
        OperatorMapper::resolve('invalid');
    }

    public function testGetSupportedOperators(): void
    {
        $supportedOperators = OperatorMapper::getSupportedOperators();
        $this->assertIsArray($supportedOperators);
        $this->assertContains('is null', $supportedOperators);
        $this->assertContains('not null', $supportedOperators);
        $this->assertContains('is true', $supportedOperators);
        $this->assertContains('is false', $supportedOperators);
        $this->assertContains('is_null', $supportedOperators);
        $this->assertContains('is_not_null', $supportedOperators);
        $this->assertContains('is_true', $supportedOperators);
        $this->assertContains('is_false', $supportedOperators);
    }

    public function testIsSupported(): void
    {
        $this->assertEquals(true, OperatorMapper::isSupported('gte'));
        $this->assertEquals(true, OperatorMapper::isSupported('like'));
        $this->assertEquals(true, OperatorMapper::isSupported('is null'));
        $this->assertEquals(true, OperatorMapper::isSupported('is_not_null'));
    }

    public function testIsSupportedInvalid(): void
    {
        $this->assertEquals(false, OperatorMapper::isSupported('pish pish ololo, i am driver nlo'));
        $this->assertEquals(false, OperatorMapper::isSupported('EQ'));
    }
}
