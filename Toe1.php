<?php

namespace ZabbixTets;

use Exception;

/**
 * Structure for a single game board
 */
class Board
{
    /** @var array<int, array<int, string[]>> */
    public array $lines;

    public function __construct (array $lines) {
        $this->lines = $lines;
    }
}

class BoardHelper
{
    private Board $board;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    /**
     * Get the count of given symbols on the board
     * 
     * @param string $symbol Symbol to search: X or O
     */
    public function getCount(string $symbol): int
    {
        $count = 0;

        foreach ($this->board->lines as $line) {
            foreach ($line as $cell) {
                if ($cell === $symbol) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Get the count of bingos for given symbol
     */
    public function getBingoCount(string $symbol): int
    {
        $count = 0;

        // Count all horizonal bingos
        foreach ($this->board->lines as $line) {
            if (array_all($line, fn ($currentSymbol) => $currentSymbol === $symbol)) {
                $count++;
            }
        }

        // Count all vertical bingos
        for ($i = 0; $i < 3; $i++) {
            $line = [$this->board->lines[0][$i], $this->board->lines[1][$i], $this->board->lines[2][$i]];
            if (array_all($line, fn ($currentSymbol) => $currentSymbol === $symbol)) {
                $count++;
            }
        }

        
        // Count diagonal bingos
        if (
            $this->board->lines[1][1] === $symbol
            && (
                ($this->board->lines[0][0] === $symbol && $this->board->lines[2][2] === $symbol)
                || ($this->board->lines[0][2] === $symbol && $this->board->lines[2][0] === $symbol)
            )
        ) {
            $count++;
        }

        return $count;
    }
}

/**
 * Parse input text data
 * 
 * @return Board[]
 * 
 * @throws Exception
 */

function parseInput(string $input): array
{
    $count = (int) substr($input, 0, strpos($input, "\n"));
    if ($count === 0) {
        throw new Exception("No boards given.");
    }
    
    $inputWithoutBoardsCount = substr($input, strpos($input, "\n") + 1);

    $boards = [];
    $rawBoards = explode("\n\n", $inputWithoutBoardsCount);
    foreach ($rawBoards as $i => $lines) {
        $lines = explode("\n", $lines);

        array_walk($lines, function (&$boardLine) use ($i) {
            $boardLine = str_split($boardLine);
        });

        if (count($lines) !== 3) {
            throw new Exception("Board %d has incorrect number of lines");
        }

        foreach ($lines as $line) {
            if (count($line) !== 3) {
                throw new Exception("Board %d has incorrect number of columns");
            }
        }

        $boards[] = new Board($lines);
    }

    if ($count !== count($boards)) {
        throw new Exception("Provided board count number doesn't match the actual provided board count.");
    }

    return $boards;
}

/**
 * Check if the given board contains a possible game combination
 * 
 * @param Board $board
 */
function validateBoard(Board $board): bool
{
    $helper = new BoardHelper($board);

    // X starts first, players make turns one after another, which means "0 <= (cnt(X) - cnt(O)) <= 1"

    // Equal amount of sybols means either a draw or a win for O
    if (
        $helper->getCount('X') === $helper->getCount('O')
        && $helper->getBingoCount('X') === 0
        && $helper->getBingoCount('O') >= 0
    ) {
        return true;
    }

    // After the winning turn for X it will always be "cnt(X) - cnt(O) == 1"
    if (
        $helper->getCount('X') - $helper->getCount('O') === 1
        && $helper->getBingoCount('X') >= 0
        && $helper->getBingoCount('O') === 0
    ) {
        return true;
    }

    return false;
}


// --- Console output ---
$input = "4\nX.O\nOO.\nXXX\n\nO.X\nXX.\nOOO\n\nX.O\nOO.\nX.X\n\nX..\n...\n...";

$boards = parseInput($input);

$output = '';
foreach ($boards as $board) {
    $output .= (validateBoard($board) ? 'yes' : 'no')."\n";
}

echo $output;
