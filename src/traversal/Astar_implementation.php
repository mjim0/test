<?php
error_reporting(0);
$jsonData = file_get_contents('BALDY_ALL_FLOORS.json');
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

//--------------------------------------------------------------------------

function getFloorNumber($roomNumber) {
    $patterns = array(
        '/Room-WEB-(\d{1})/', //Room-WEB-XXX
        '/Room-WEB-S(\d{1})/', //Room-WEB-SXXX format
        '/Room-WEB-C(\d{1})/', // Room-WEB-CXXX format
        '/EXT(\d{1})/',        //EXTXXX format

    );

    // Try to match the room number against each pattern
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $roomNumber, $matches)) {
            return intval($matches[1]); #int val
        }
    }

    return -1; //invalid
}

function loadFloorData($floorNumber, $directory) {
    $fileName = $directory . "/BALDY0" . $floorNumber . ".json";
    echo "Loading floor data from: " . $fileName . PHP_EOL;
    $jsonData = file_get_contents($fileName);
    return json_decode($jsonData, true);
}

#---------------------------------------------------------------------
/*
function findStaircase($floorData) {
    foreach ($floorData as $room => $connections) {
        if (strpos($room, "Room-WEB-S") === -1) {
            return $room;
        }
    }
    return null; // Staircase not found
}

//Does not work

function findPath($startPoint, $endPoint, $directory) {
    $currentRoom = $startPoint;
    $currentFloor = getFloorNumber($currentRoom);
    $endFloor = getFloorNumber($endPoint);
    $path = array();

    // If start and end points are on different floors, find path between floors
    if ($currentFloor != $endFloor) {
        while ($currentFloor != $endFloor) {
            $floorData = loadFloorData($currentFloor, $directory);
            $staircase = findStaircase($floorData);

            // Find path to staircase
            $pathToStaircase = aStarSearch($floorData, $currentRoom, $staircase);
            if ($pathToStaircase === null) {
                return null; // No path found
            }

            // Add path to staircase to overall path
            $path = array_merge($path, $pathToStaircase);

            // Move to next floor
            $currentFloor++;
            $currentRoom = $staircase;
        }
    }

    $floorData = loadFloorData($currentFloor, $directory);
    $pathWithinFloor = aStarSearch($floorData, $currentRoom, $endPoint);
    if ($pathWithinFloor === null) {
        return null;
    }
    $path = array_merge($path, $pathWithinFloor);

    return $path;
}
*/
// Define the directory where JSON files are located
$directory = "C:\Users\mdjim\PhpstormProjects\phpFirstTime";
/*
$startPoint = "Room-WEB-301";
$endPoint = "Room-WEB-311";

$path = findPath($startPoint, $endPoint, $directory);

if ($path !== null) {
    echo "Path found: " . implode(" -> ", $path);
} else {
    echo "No path found from $startPoint to $endPoint.";
}

*/

$startPoint = "Room-WEB-C103";
$endPoint = "Room-WEB-209";

$path = aStarSearch($graph, $startPoint, $endPoint);

if ($path !== null) {
    echo "Path found: " . implode(" -> ", $path);
} else {
    echo "No path found from $startPoint to $endPoint.";
}

