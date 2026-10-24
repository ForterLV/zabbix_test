<?php

namespace ZabbixTets;

use Exception;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once('../Toe1.php');

class Toe1Test extends TestCase
{
    public function testParseValidInputWithSymbols()
    {
        $input = "4\nX.O\nOO.\nXXX\n\nO.X\nXX.\nOOO\n\nX.O\nOO.\nX.X\n\nX..\n...\n...";
        
        $parsedInput = parseInput($input);

        static::assertCount(4, $parsedInput);
    }

    public function testParseInputWithWrongBlockCount()
    {
        $input = "3\nX.O\nOO.\nXXX\n\nO.X\nXX.\nOOO\n\nX.O\nOO.\nX.X\n\nX..\n...\n...";

        static::expectException(Exception::class);
        static::expectExceptionMessageIs("Provided board count number doesn't match the actual provided board count.");

        $parsedInput = parseInput($input);
    }

    public function testParseInputWithWrongBlockLineCount()
    {
        $input = "1\nX.O\nOO.\nXXX\n...";
        
        static::expectException(Exception::class);
        static::expectExceptionMessageIsOrContains("incorrect number of lines");

        $parsedInput = parseInput($input);
    }

    public function testParseInputWithWrongBlockColumnsCount()
    {
        $input = "1\nX.O\nOO.\nXXXX";
        
        static::expectException(Exception::class);
        static::expectExceptionMessageIsOrContains("incorrect number of columns");

        $parsedInput = parseInput($input);
    }

    #[DataProvider('provideBoards')]
    public function testValidateInputWithValidBoard(Board $board, bool $expected)
    {
        static::assertEquals($expected, validateBoard($board));
    }

    public static function provideBoards(): Generator
    {
        yield [new Board([['.','.','.'],['.','.','.'],['.','.','.']]), true];
        yield [new Board([['X','.','.'],['.','.','.'],['.','.','.']]), true];
        yield [new Board([['X','X','.'],['.','.','.'],['.','.','.']]), false];
        yield [new Board([['X','X','.'],['.','O','.'],['.','.','.']]), true];
        yield [new Board([['X','X','X'],['O','O','O'],['.','.','.']]), false]; // Two winners
        yield [new Board([['X','X','X'],['X','O','O'],['X','O','O']]), true];
        yield [new Board([['X','O','X'],['.','X','O'],['.','O','X']]), true];
        yield [new Board([['X','.','X'],['O','O','O'],['.','.','.']]), false]; // O more than X
    }

    public function testBoardHelperSymbolCount()
    {
        $helper = new BoardHelper(new Board([['X','X','X'],['X','O','O'],['X','O','O']]));

        static::assertEquals(5, $helper->getCount('X'));
        static::assertEquals(4, $helper->getCount('O'));
        static::assertEquals(2, $helper->getBingoCount('X'));
        static::assertEquals(0, $helper->getBingoCount('O'));
    }
}
