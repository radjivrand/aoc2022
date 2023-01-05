<?php

// define the maze as a 2-dimensional array
$maze = [
    [1, 1, 1, 1, 1, 1, 1, 1],
    [1, 0, 0, 0, 0, 0, 0, 1],
    [1, 0, 1, 1, 1, 1, 0, 1],
    [1, 0, 1, 0, 0, 1, 0, 1],
    [1, 0, 1, 0, 1, 1, 0, 1],
    [1, 0, 1, 0, 0, 0, 0, 1],
    [1, 0, 1, 1, 1, 1, 0, 1],
    [1, 0, 0, 0, 0, 0, 0, 1],
    [1, 1, 1, 1, 1, 1, 1, 1]
];

// create a graph representation of the maze
$graph = [];
for ($i = 0; $i < count($maze); $i++) {
    for ($j = 0; $j < count($maze[$i]); $j++) {
        if ($maze[$i][$j] == 0) { // add nodes for open spaces in the maze
            $graph["$i,$j"] = [];
            if ($maze[$i-1][$j] == 0) { // add an edge to the node above
                $graph["$i,$j"][] = ["$i-1,$j", 1];
            }
            if ($maze[$i+1][$j] == 0) { // add an edge to the node below
                $graph["$i,$j"][] = ["$i+1,$j", 1];
            }
            if ($maze[$i][$j-1] == 0) { // add an edge to the node to the left
                $graph["$i,$j"][] = ["$i,$j-1", 1];
            }
            if ($maze[$i][$j+1] == 0) { // add an edge to the node to the right
                $graph["$i,$j"][] = ["$i,$j+1", 1];
            }
        }
    }
}

// output the graph representation
print_r($graph);
