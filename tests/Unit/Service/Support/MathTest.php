<?php

declare(strict_types=1);

namespace AlgoYounes\CommissionTask\Tests\Unit\Service\Support;

use AlgoYounes\CommissionTask\Support\Math;
use AlgoYounes\CommissionTask\Tests\TestCase;

class MathTest extends TestCase
{
    public function test_add(): void
    {
        $this->assertEquals('5.0000000000', Math::add('2.5', '2.5'));
        $this->assertEquals('0.0000000000', Math::add('0', '0'));
    }

    public function test_subtract(): void
    {
        $this->assertEquals('1.0000000000', Math::subtract('3.5', '2.5'));
        $this->assertEquals('-2.5000000000', Math::subtract('0', '2.5'));
    }

    public function test_multiply(): void
    {
        $this->assertEquals('6.2500000000', Math::multiply('2.5', '2.5'));
        $this->assertEquals('0.0000000000', Math::multiply('0', '100'));
    }

    public function test_divide(): void
    {
        $this->assertEquals('1.0000000000', Math::divide('5', '5'));
        $this->assertEquals('0.5000000000', Math::divide('1', '2'));
    }

    public function test_round(): void
    {
        $this->assertEquals('2.457', Math::roundUp('2.4567', 3));
        $this->assertEquals('3', Math::roundUp('2.4456'));
    }

    public function test_compare(): void
    {
        $this->assertEquals(0, Math::compare('2.5', '2.5'));
        $this->assertEquals(1, Math::compare('3.5', '2.5'));
        $this->assertEquals(-1, Math::compare('2.5', '3.5'));
    }

    public function test_greater_than(): void
    {
        $this->assertTrue(Math::gt('3.5', '2.5'));
        $this->assertFalse(Math::gt('2.5', '3.5'));
        $this->assertFalse(Math::gt('2.5', '2.5'));
    }

    public function test_round_up_with_different_precisions(): void
    {
        $this->assertEquals(0.03, Math::roundUp('0.023', 2));
        $this->assertEquals(0.02, Math::roundUp('0.0201', 2));
    }
}
