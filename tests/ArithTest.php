<?php

namespace ZabbixTets;

use Exception;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once('../Arith.php');

class ArithTest extends TestCase
{
    public function testParseValidInput()
    {
        $input = "4\n12345+67890\n324-111\n325*4405\n1234*4";

        $parsedInput = parseInput($input);

        static::assertCount(4, $parsedInput);

    }

    public function testParseInputWithWrongLineCount()
    {
        $input = "3\n12345+67890\n324-111\n325*4405\n1234*4";

        static::expectException(Exception::class);
        static::expectExceptionMessageIs("Provided board count number doesn't match the actual provided board count.");

        $parsedInput = parseInput($input);
    }

    public function testParseInputWithWrongOperationType()
    {
        $input = "4\n12345+67890\n324-111\n325*4405\n1234/4";

        static::expectException(Exception::class);
        static::expectExceptionMessageIsOrContains("Bad input on line");

        $parsedInput = parseInput($input);
    }

    #[DataProvider('provideBlocks')]
    public function testCalculate(Block $block, int $expected)
    {
        calculate($block);
        static::assertEquals($expected, $block->result);
    }

    public static function provideBlocks(): Generator
    {
        yield [new Block(12345, 67890, '+'), 80235];
        yield [new Block(324, 111, '-'), 213];
        yield [new Block(325, 4405, '*'), 1431625];
    }

    #[DataProvider('provideFormattedBlocks')]
    public function testFormatBlock(Block $block, string $expected)
    {
        calculate($block);
        static::assertEquals($expected, formatBlock($block));
    }

    public static function provideFormattedBlocks(): Generator
    {
        yield [new Block(12345, 67890, '+'), ' 12345
+67890
------
 80235'];
        yield [new Block(325, 4405, '*'), '    325
  *4405
  -----
   1625
     0 
 1300  
1300   
-------
1431625'];
        yield [new Block(1234, 4, '*'), '1234
  *4
----
4936'];
        yield [new Block(1234, 0, '*'), '1234
  *0
  --
   0'];
    }
}
