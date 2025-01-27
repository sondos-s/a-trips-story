<?php
// Include the existing database connection from UDB.php
$mysqli = require __DIR__ . "/UDB.php";

// Get the trip ID from the query parameters
if (isset($_GET['trip_id'])) {
    $tripId = $_GET['trip_id'];

    // Fetch the trip details from the database (similar to TripDetails_FrontEnd.php)
    $query = "SELECT * FROM trips WHERE tripId = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $tripId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the trip data
        $tripData = $result->fetch_assoc();

        // Fetch location name based on tripLocation ID
        $locationQuery = "SELECT locationName FROM locations WHERE id = ?";
        $locationStmt = $mysqli->prepare($locationQuery);
        $locationStmt->bind_param("i", $tripData['tripLocation']);
        $locationStmt->execute();
        $locationResult = $locationStmt->get_result();

        if ($locationResult->num_rows > 0) {
            $locationData = $locationResult->fetch_assoc();
            $locationName = $locationData['locationName'];

            // Use Google Maps Geocoding API to get coordinates
            $apiKey = 'AIzaSyApmIjM2bI6swuqNlvPQTOaI0wyhGk_uc8'; // Replace with your API key
            $encodedLocationName = urlencode($locationName);
            $geocodingUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$encodedLocationName}&key={$apiKey}";

            // Make the API request
            $geocodingResponse = file_get_contents($geocodingUrl);
            $geocodingData = json_decode($geocodingResponse, true);

            if ($geocodingData['status'] === 'OK' && isset($geocodingData['results'][0]['geometry']['location'])) {
                $latitude = $geocodingData['results'][0]['geometry']['location']['lat'];
                $longitude = $geocodingData['results'][0]['geometry']['location']['lng'];

                // Fetch weather information using OpenWeatherMap API
                $openWeatherMapApiKey = '3045dd712ffe6e702e3245525ac7fa38'; // Replace with your OpenWeatherMap API key
                $apiUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$openWeatherMapApiKey}";

                // Make the API request to OpenWeatherMap
                $weatherData = file_get_contents($apiUrl);

                // Check if the request was successful
                if ($weatherData) {
                    // Parse the JSON response
                    $weatherInfo = json_decode($weatherData, true);

                    // Extract relevant weather information
                    $temperatureKelvin = $weatherInfo['main']['temp'];
                    $temperatureCelsius = round($temperatureKelvin - 273.15); // Convert from Kelvin to Celsius and round it
                    $description = $weatherInfo['weather'][0]['description'];

                    echo "<div id='weather-info'>";
                    echo "<article class='box weather' style='text-align: left; line-height: 0.5;'>";
                    echo "<h4 style=\"font-size: 20px;\">&nbsp;&nbsp;{$locationName}&nbsp;<i class='fa fa-location-arrow'></i></h4>"; // Use the trip location
                    echo "<p class='temp' style=\"font-size: 30px;\"> &nbsp;" . number_format($temperatureCelsius, 0) . "°C</p>"; // Temperature in the middle
                    
                    $emoji = '';

                    switch ($description) {
                        case 'mist':
                            $emoji = '🌫️'; // Mist
                            break;
                        case 'snow':
                            $emoji = '❄️'; // Snow
                            break;
                        case 'thunderstorm':
                            $emoji = '⛈️'; // Thunderstorm
                            break;
                        case 'rain':
                            $emoji = '🌧️'; // Rain
                            break;
                        case 'shower rain':
                            $emoji = '🌦️'; // Shower Rain
                            break;
                        case 'broken clouds':
                            $emoji = '☁️'; // Broken Clouds
                            break;
                        case 'scattered clouds':
                            $emoji = '⛅'; // Scattered Clouds
                            break;
                        case 'few clouds':
                            $emoji = '🌤️'; // Few Clouds
                            break;
                        case 'clear sky':
                            $emoji = '☀️'; // Clear Sky
                            break;
                        case 'fog':
                            $emoji = '🌫️'; // Fog
                            break;
                        case 'overcast clouds':
                            $emoji = '☁️'; // Overcast Clouds
                            break;
                        case 'heavy rain':
                            $emoji = '🌧️☔'; // Heavy Rain
                            break;
                        case 'light rain':
                            $emoji = '🌦️'; // Light Rain
                            break;
                        case 'freezing rain':
                            $emoji = '🌧️❄️'; // Freezing Rain
                            break;
                        case 'sleet':
                            $emoji = '🌧️❄️'; // Sleet
                            break;
                        case 'tornado':
                            $emoji = '🌪️'; // Tornado
                            break;
                        case 'hurricane':
                            $emoji = '🌀'; // Hurricane
                            break;
                        case 'blizzard':
                            $emoji = '🌨️'; // Blizzard
                            break;
                        case 'sandstorm':
                            $emoji = '🌪️🏜️'; // Sandstorm
                            break;
                        case 'smoke':
                            $emoji = '🌫️🔥'; // Smoke
                            break;
                        default:
                            $emoji = ''; // Default emoji or no emoji for other descriptions
                            break;
                    }
                    echo "<br>";
                    echo "<p style=\"font-size: 18px;\">&nbsp;&nbsp;&nbsp;" . $emoji . "</p>"; // with emoji

                    echo "<p style=\"font-size: 16px;\"> &nbsp;&nbsp;&nbsp;" . $description . "</p>"; // Description at the bottom
                    echo "</article>";
                    echo "</div>";
                } else {
                    // Handle the case where the API request to OpenWeatherMap failed
                    echo "Failed to retrieve weather data.";
                }
            } else {
                $locationName = "Unknown Location";
            }
        } else {
            $locationName = "Unknown Location";
        }
    } else {
        echo "<p>No trip found with the given ID.</p>";
    }
} else {
    echo "<p>Trip ID not provided.</p>";
}



?>