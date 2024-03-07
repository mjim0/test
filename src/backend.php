<?php
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $postData = file_get_contents('php://input');

    // Decode JSON data
    $jsonData = json_decode($postData);

    // Check if JSON data was successfully decoded
    if ($jsonData !== null) {
        // Check if latitude and longitude are present in the JSON data
        if (isset($jsonData->latitude) && isset($jsonData->longitude)) {
            // Print latitude and longitude
            echo "Successfully pushed data via POST: \n";
            echo "The current latitude is: " . $jsonData->latitude . "\n";
            echo "The current longitude is: " . $jsonData->longitude;
        } else {
            // If latitude or longitude is missing, print an error message
            echo "Error: Latitude or longitude is missing in the request data.";
        }
    } else {
        // If JSON decoding fails, print an error message
        echo "Error: Failed to decode JSON data.";
    }
} else {
    // If the request method is not POST, print an error message
    echo "Error: Only POST requests are allowed.";
}
?>