<?php
error_reporting(0);
$jsonData = file_get_contents('BALDY01.json');
$adjacencyList = json_decode($jsonData, true);

$graph = array();
// Populate the graph using the adjacency list
foreach ($adjacencyList as $node => $connections) {
    $x = (float) $connections[0][0];
    $y = (float) $connections[0][1];
    $graph[$node] = array("coords" => array($x, $y), "neighbors" => $connections[1]);
}

function euclideanDistance($p1, $p2) {
    $x1 = $p1[0];
    $y1 = $p1[1];
    $x2 = $p2[0];
    $y2 = $p2[1];
    return sqrt(($x2 - $x1) * ($x2 - $x1) + ($y2 - $y1) * ($y2 - $y1));
}

function reconstructPath($cameFrom, $current) {
    $totalPath = array($current);
    while (isset($cameFrom[$current])) {
        $current = $cameFrom[$current];
        $totalPath[] = $current;
    }
    return array_reverse($totalPath);
}

function aStarSearch($graph, $start, $goal) {
    $openSet = array($start);
    $cameFrom = array();
    $gScore = array_fill_keys(array_keys($graph), INF);
    $gScore[$start] = 0;
    $fScore = array_fill_keys(array_keys($graph), INF);
    $fScore[$start] = euclideanDistance($graph[$start]["coords"], $graph[$goal]["coords"]);

    while (!empty($openSet)) {
        $current = null;
        $minFScore = INF;
        foreach ($openSet as $node) {
            if ($fScore[$node] < $minFScore) {
                $minFScore = $fScore[$node];
                $current = $node;
            }
        }

        if ($current === $goal) {
            return reconstructPath($cameFrom, $goal);
        }

        $key = array_search($current, $openSet);
        if ($key !== false) {
            unset($openSet[$key]);
        }

        foreach ($graph[$current]["neighbors"] as $neighbor) {
            $tentative_gScore = $gScore[$current] + euclideanDistance($graph[$current]["coords"], $graph[$neighbor]["coords"]);
            if ($tentative_gScore < $gScore[$neighbor]) {
                $cameFrom[$neighbor] = $current;
                $gScore[$neighbor] = $tentative_gScore;
                $fScore[$neighbor] = $gScore[$neighbor] + euclideanDistance($graph[$neighbor]["coords"], $graph[$goal]["coords"]);
                if (!in_array($neighbor, $openSet)) {
                    $openSet[] = $neighbor;
                }
            }
        }
    }

    return null;
}





$startPoint = "Room-WEB-101";
$endPoint = "Room-WEB-S119";

$path = aStarSearch($graph, $startPoint, $endPoint);

if ($path !== null) {
    echo "Path found: " . implode(" -> ", $path);
} else {
    echo "No path found from $startPoint to $endPoint.";
}


