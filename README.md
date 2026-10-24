# Zabbix test tasks

## Intro
Here are the solutions for two tasks:
- toe1.php, TOE 1, https://spoj.com/problems/TOE1/
- arith.php, ARITH, https://www.spoj.com/problems/ARITH/

You can find both submissions on my profile page: https://spoj.com/users/j_sulins/

First I implemented a solution for TOE 1, but then decided to go with additional task. I found them pretty interesting, so it was a pleasure. At some point I spent a lot of time solving similar puzzles on LeetCode :)
For some reason the solution for ARITH was not accepted, despite the result matches the example perfectly. I double checked all the condition described in task, and I'm sure my algo satisfies them.

Anyway, here in this repo I decided to go further, not only making the code compatible with later versions of PHP (8+) comparing to spoj.com (7.3), but by also implementing tests.
Of course, the way the code is structured is a bit overkill for such a small task. But at the same time it allows me to showcase mode approaches, like, decomposing the task, validationg the solution.

For these tasks I set myself few guardrails. Not only because of our conversation and your advice in email, but mainly because I find it fair.
- No AI at all, even for generating algorithm, ideas, advices, or anything else
- No googling for solutions, ideas, etc.
- No reading the comments under tasks


## TOE 1
Despite the game may have thousands of possible valid combinations, the conditions of a valid board are simple:
- Considering the X player always makes the first turn, the amount of X will always be equal or one more than the amount of O
- When one player wins there shouldn't be a winning combination for another player
- When O is the winner the amount of X and O can only be equal
- When X is the winner or there's a draw the amount of X can be equal or O+1

All these conditions are covered by two checks in `validateBoard` function.

The structure is simple:
1. `parseInput` function reads the text input and prepares the list of typed Board objects
2. `validateBoard` function uses `BoardHelper` class under the hood to get the game-specific data, like the amount of each symbol, the amount of winning lines (bingos) and validate them according to conditions described above


## ARITH
Addition and substraction are easy, with multiplication it's a bit trickier.
For multiplication we need a result for every digit of the second number. In PHP it's pretty easy to use string as array of symbols, which combined with casting gives an easy way to get the result.
No `eval` here, that's a major red flag for me.
1. `parseInput` function reads the input and prepares the list of types Block objects
2. `calculate` function calculates the result and also prepare the list of intermediate results in case of multiplication
2. `formatBlock` is called for each calculated block to format the output by padding all the numbers according to specifications