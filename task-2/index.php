<?php

/**
 * Task 2: Hidden Item Game
 */
class HiddenItemGame
{
    private array $grid;
    private int $startX = -1;
    private int $startY = -1;
    private array $probableLocations = [];

    public function __construct(array $grid)
    {
        $this->grid = $grid;
        $this->findStartPosition();
    }

    /**
     * Find the coordinates (X, Y) of the starting position 'X'.
     */
    private function findStartPosition(): void
    {
        foreach ($this->grid as $y => $row) {
            $x = strpos($row, 'X');
            if ($x !== false) {
                $this->startX = $x;
                $this->startY = $y;
                return;
            }
        }
    }

    /**
     * Simulate movements to find all probable item locations.
     * Rule: Up/North A step(s) -> Right/East B step(s) -> Down/South C step(s).
     */
    public function findProbableLocations(): void
    {
        $rows = count($this->grid);
        $cols = strlen($this->grid[0]);

        // Move Up/North (A steps)
        for ($a = 1; $a < $rows; $a++) {
            if (!$this->isPathClear($this->startX, $this->startY, 0, -$a)) continue;
            $yAfterA = $this->startY - $a;

            // Move Right/East (B steps)
            for ($b = 1; $b < $cols; $b++) {
                if (!$this->isPathClear($this->startX, $yAfterA, $b, 0)) continue;
                $xAfterB = $this->startX + $b;

                // Move Down/South (C steps)
                for ($c = 1; $c < $rows; $c++) {
                    if (!$this->isPathClear($xAfterB, $yAfterA, 0, $c)) continue;
                    $yAfterC = $yAfterA + $c;

                    // Store the valid destination coordinate
                    $coordKey = "$xAfterB,$yAfterC";
                    $this->probableLocations[$coordKey] = [
                        'x' => $xAfterB, 
                        'y' => $yAfterC
                    ];
                }
            }
        }
    }

    /**
     * Check if the straight path between a starting point and a destination is free of obstacles (#).
     */
    private function isPathClear(int $startX, int $startY, int $deltaX, int $deltaY): bool
    {
        $stepX = $deltaX === 0 ? 0 : ($deltaX > 0 ? 1 : -1);
        $stepY = $deltaY === 0 ? 0 : ($deltaY > 0 ? 1 : -1);

        $steps = max(abs($deltaX), abs($deltaY));
        $currentX = $startX;
        $currentY = $startY;

        for ($i = 0; $i < $steps; $i++) {
            $currentX += $stepX;
            $currentY += $stepY;

            // Stop if it goes out of bounds or hits an obstacle (#)
            if (!isset($this->grid[$currentY][$currentX]) || $this->grid[$currentY][$currentX] === '#') {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Display the possible coordinates and the modified grid with '$' (Bonus Points).
     */
    public function displayResults(): void
    {
        echo "Probable Item Coordinates (X, Y):\n";
        echo "---------------------------------\n";
        
        if (empty($this->probableLocations)) {
            echo "None found.\n";
        } else {
            foreach ($this->probableLocations as $loc) {
                echo "- (X: " . $loc['x'] . ", Y: " . $loc['y'] . ")\n";
            }
        }

        echo "\nGrid with Probable Locations ($):\n";
        echo "---------------------------------\n";
        
        $modifiedGrid = $this->grid;
        
        // Mark the probable locations with '$'
        foreach ($this->probableLocations as $loc) {
            $modifiedGrid[$loc['y']][$loc['x']] = '$';
        }

        // Output the final grid
        foreach ($modifiedGrid as $row) {
            echo $row . "\n";
        }
        echo "\n";
    }
}

// 1. The initial grid provided in the assessment test
$initialGrid = [
    "########",
    "#......#",
    "#.###..#",
    "#...#.##",
    "#X#....#",
    "########"
];

// 2. Initialize and run the game logic
$game = new HiddenItemGame($initialGrid);
$game->findProbableLocations();
$game->displayResults();

?>