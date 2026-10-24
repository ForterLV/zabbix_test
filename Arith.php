<?php

namespace ZabbixTets;

use Exception;

/**
 * Structure for a single calculation block
 */
class Block
{
    public $operand1;
    public $operand2;
    public $operator;
    public $extraLines = [];
    public $result = 0;

    public function __construct(int $operand1, int $operand2, string $operator)
    {
        $this->operand1 = $operand1;
        $this->operand2 = $operand2;
        $this->operator = $operator;
    }
}

/**
 * Parse input text data
 * @param string $input
 * @throws Exception
 * @return Block[]
 */
function parseInput(string $input)
{
    $count = (int) substr($input, 0, strpos($input, "\n"));
    if ($count === 0) {
        throw new Exception("No input.");
    }
    
    $inputWithoutCount = trim(substr($input, strpos($input, "\n") + 1));
    
    $rawLines = explode("\n", $inputWithoutCount);
    if ($count !== count($rawLines)) {
        throw new Exception("Provided board count number doesn't match the actual provided board count.");
    }

    /** @var Block[] */
    $blocks = [];

    foreach ($rawLines as $i => $line) {
        if (!preg_match('/([0-9]+)([-+*])([0-9]+)/', $line, $matches)) {
            throw new Exception("Bad input on line ".$i);
        }

        $blocks[] = new Block((int) $matches[1], (int) $matches[3], $matches[2]);
    }

    return $blocks;
}

/**
 * Calculate the result and intermediate lines if necessary (multiplication)
 * @param Block $block
 * @return void
 */
function calculate(Block $block): void
{
    if ($block->operator === '+') {
        $block->result = $block->operand1 + $block->operand2;
    } elseif ($block->operator === '-') {
        $block->result = $block->operand1 - $block->operand2;
    } else {
        $block->result = $block->operand1 * $block->operand2;

        $operand2str = (string) $block->operand2;
        for ($i = strlen($operand2str) - 1; $i >= 0; $i--) {
            $block->extraLines[] = $block->operand1 * $operand2str[$i];
        }
    }
}

/**
 * Set padding on the left within a single block to have a proper alignment by the right side
 */
function formatBlock(Block $block): string
{
    $block->extraLines = formatExtraLines($block->extraLines);
    
    $blockWidth = max(array_merge([strlen($block->result), strlen($block->operand1), strlen($block->operand2) + 1], array_map('strlen', $block->extraLines)));

    $resultLines = [];
    
    $resultLines[] = str_pad($block->operand1, $blockWidth, ' ', STR_PAD_LEFT);
    $resultLines[] = str_pad($block->operator.$block->operand2, $blockWidth, ' ', STR_PAD_LEFT);

    // Let's find the length of the first divider
    $beforeAndAfterDivider = [];
    // Before the line there's the second operand
    $beforeAndAfterDivider[] = strlen($block->operand2) + 1;
    // After the line there's the first extra line of multiplication (in case there's more than one), or the result
    $beforeAndAfterDivider[] = count($block->extraLines) > 1 ? strlen($block->extraLines[0]) : strlen($block->result);

    $firstDividerLength = max($beforeAndAfterDivider);
    
    $resultLines[] = str_pad(str_repeat('-', $firstDividerLength), $blockWidth, ' ', STR_PAD_LEFT);

    if (count($block->extraLines) > 1) {
        foreach ($block->extraLines as $line) {
            $resultLines[] = str_pad($line, $blockWidth, ' ', STR_PAD_LEFT);
        }

        $resultLines[] = str_repeat('-', max(strlen(end($resultLines)), strlen($block->result)));
    }

    $resultLines[] = str_pad($block->result, $blockWidth, ' ', STR_PAD_LEFT);

    return implode("\n", $resultLines);
}

/**
 * Set padding on the right for intermediate lines in multiplication

 * @param string[] $extraLines
 * @return string[]
 */
function formatExtraLines(array $extraLines)
{
    $formattedLines = [];

    foreach ($extraLines as $i => $line) {
        $formattedLines[] = $line . str_repeat(' ', $i);
    }

    return $formattedLines;
}


// --- Console output ---
$input = "4
12345+67890
324-111
325*4405
1234*4";

$blocks = parseInput($input);
$formattedBlocks = [];

foreach ($blocks as $block) {
    calculate($block);
    $formattedBlocks[] = formatBlock($block);
}

$output = implode("\n\n", $formattedBlocks)."\n\n";

echo $output;
