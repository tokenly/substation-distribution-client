<?php

use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{

    public function testArrayCount() {
        PHPUnit::assertCount(3, ['a','b','c']);
    } 

}
