<?php
#error_reporting(E_ALL)
error_reporting(0);
#ini_set('assert.active', 1);
#ini_set('assert.exception', 1);
#require_once 'generic_trav.php';

$jsonData = file_get_contents('BALDY.json');
$adjacencyList = json_decode($jsonData, true);

$graph = array();
// Populate the graph using the adjacency list
foreach ($adjacencyList as $node => $connections) {
    $x = (float) $connections[0][0];
    $y = (float) $connections[0][1];
    $graph[$node] = array("coords" => array($x, $y), "neighbors" => $connections[1]);
}


function loadGraphData($building) {
    $jsonData = file_get_contents("$building.json");
    $adjacencyList = json_decode($jsonData, true);

    $graph = array();
    // Populate the graph using the adjacency list
    foreach ($adjacencyList as $node => $connections) {
        $x = (float) $connections[0][0];
        $y = (float) $connections[0][1];
        $graph[$node] = array("coords" => array($x, $y), "neighbors" => $connections[1]);
    }

    return $graph;
}


function Building($input) {
    $parts = explode('-', $input);
    return $parts[0];
}

function hasPath($graph, $start, $target, $visited = []) {
    if (!isset($graph[$start])) {
        return [false, []];
    }
    $visited[] = $start;
    if (in_array($target, $graph[$start])) {
        return [true, [$start, $target]];
    }
    foreach ($graph[$start] as $vertex) {
        // If the adjacent vertex hasn't been visited, recursively check for a path from it
        if (!in_array($vertex, $visited)) {
            // Recursively check for a path from the current vertex
            list($hasPath, $path) = hasPath($graph, $vertex, $target, $visited);
            if ($hasPath) {
                // Path found, prepend the current vertex and return the path
                array_unshift($path, $start);
                return [true, $path];
            }
        }
    }

    // No path found
    return [false, []];
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

#-----------------------------------------------------
function SEARCH($graph, $start, $goal) {
    if (Building($start) == Building($goal)) {
        //echo "yes";
        // Same building, use A* search directly
        $graph = loadGraphData(Building($start));
        //var_dump($graph);
        $path = aStarSearch($graph, $start, $goal);
        //var_dump($path[0]);
        if ($path !== null) {
            echo "Complete Path: " . implode(" -> ", $path);
            return [true, $path];
        } else {
            echo "No path found between $start and $goal within the same building.";
            return [false, []];
        }
    } else {
        $graphFile = 'connections.json';
        $graphData = json_decode(file_get_contents($graphFile), true);
        $startVertex = Building($start);
        $targetVertex = Building($goal);
        $path = hasPath($graphData, $startVertex, $targetVertex);

        $completePath = array();
        if ($path[0]) {
            //var_dump($path);
            $pathList = $path[1];
            $numBuildings = count($pathList);
            var_dump($numBuildings);

            #echo $targetVertex;
            $graph = loadGraphData(Building($start));
            $initial_search = aStarSearch($graph, $start, $targetVertex);
            //math-entry to
            //problem with $targetvertex

            if ($initial_search !== null) {
                $completePath = array_merge($completePath, $initial_search);
            }
            var_dump($path);
            $counter = 1;
            while ($counter < $numBuildings) {
                $building = $pathList[$counter];
                // Load graph data for the current building n determine if this is the last building in the path
                $graph = loadGraphData($building);
                $isLastBuilding = ($counter === count($pathList) - 1);
                // Determine the target for the A* search based on whether this is the last building
                $searchTarget = $isLastBuilding ? $goal : $pathList[$counter + 1];
                // Call A* search for the current building this needs testings
                $next_search = aStarSearch($graph, $building, $searchTarget);
                if ($next_search !== null) {
                    $completePath = array_merge($completePath, $next_search);
                }
                $counter++;
            }

            // Print the complete path
            echo "Complete Path: " . implode(" -> ", $completePath);
            return [true, $completePath];
        } else {
            echo "There is no indoor path from $startVertex to $targetVertex.";
            return [false, $completePath];
        }
    }
}
#-----------------------------------------------------------------------------------------------------------------------


$startPoint2 = "BALDY-Room-WEB-101";
$endPoint2 = "BALDY-Room-WEB-216A";

$path2 = SEARCH($graph, $startPoint2, $endPoint2);




#-----------------------------------------------------------------------------------------------------------

#var_dump($path);

/*
 *  From BuildingA-Room to BuildingA-Room
 * From Building A-Room to BuildingB-Room
 * Can determine if no indoor path
 *


*/