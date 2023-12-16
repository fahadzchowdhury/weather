<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["searchBar"])) {
    // Call the API function when the form is submitted
    api();
}

// API function
function api()
{
    // Get the user input from the form
    $searchText = isset($_POST["searchBar"]) ? $_POST["searchBar"] : '';

    // Check if the search text is provided
    if (empty($searchText)) {
        echo "Please enter a location.";
        return;
    }

    // API key for OpenWeatherMap (replace with your own key)
    $apiKey = '894feb2d11ee12fc21080a731b4b35c4';

    // API endpoint for location data
    $geoApiUrl = "http://api.openweathermap.org/geo/1.0/direct?q={$searchText}&limit=1&appid={$apiKey}";

    // Perform the API request
    $geoApiResponse = file_get_contents($geoApiUrl);

    // Check if the response is valid
    if ($geoApiResponse === false) {
        echo "Error fetching data from the API.";
        return;
    }

    // Decode the JSON response
    $geoData = json_decode($geoApiResponse, true);

    // Check if location data is available
    if (empty($geoData)) {
        echo "No location found.";
        return;
    }

    // Get latitude and longitude from location data
    $lat = $geoData[0]['lat'];
    $lon = $geoData[0]['lon'];

    // API endpoint for weather data
    $weatherApiUrl = "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";

    // Perform the API request
    $weatherApiResponse = file_get_contents($weatherApiUrl);

    // Check if the response is valid
    if ($weatherApiResponse === false) {
        echo "Error fetching weather data from the API.";
        return;
    }

    // Decode the JSON response
    $weatherData = json_decode($weatherApiResponse, true);

    // Check if weather data is available
    if (empty($weatherData['list'])) {
        echo "No weather data found.";
        return;
    }

    // Echo the HTML content dynamically
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="bootstrap.min.css">';
    echo '<link rel="stylesheet" href="styles.css">';
    echo '<title>Weather Forecast</title>';
    echo '</head>';
    echo '<body class="d-flex flex-column min-vh-100">';
    echo '<div class="wrapper">';
    echo '<div class="d-flex justify-content-center">';
    echo '<form class="mt-5 d-flex " method="post" action="">';
    echo '<input name="searchBar" id="searchBar" class="form-control" type="search" placeholder=" Enter location">';
    echo '<button type="submit" class="btn btn-lg btn-success mx-2">Search</button>';
    echo '</form>';
    echo '</div>';
    echo '<div class=" container text-center" id="currentCity">';
    echo '<h6 class="text-white my-5">Current Location: ' . $weatherData['city']['name'] . ', ' . $weatherData['city']['country'] . '</h6>';
    echo '</div>';
    echo '<div id="info" class="container gap-5 mb-5 d-flex flex-wrap justify-content-center">';
    
    // Loop through the weather data and generate HTML content
    for ($i = 0; $i < min(8, count($weatherData['list'])); $i++) {
        $newDate = date('d F', strtotime($weatherData['list'][$i]['dt_txt']));
        $newTime = date('h:i A', strtotime($weatherData['list'][$i]['dt_txt']));

        echo '<div class="card" style="width: 14rem;">';
        echo '<div class="card-body">';
        echo '<div class="card-text text-center">';
        echo '<h5 class="card-title text-bold">' . $newDate . '</h5>';
        echo '<h5 class="card-title text-bold">' . $newTime . '</h5>';
        echo '<h6 class="card-text text-bold">' . $weatherData['list'][$i]['weather'][0]['main'] . '</h6>';
        echo '<h6 class="card-text">Temperature: ' . $weatherData['list'][$i]['main']['temp'] . '</h6>';
        echo '<h6 class="card-text">Feels like: ' . $weatherData['list'][$i]['main']['feels_like'] . '°C </h6>';
        echo '<h6 class="card-text">Humidity: ' . $weatherData['list'][$i]['main']['humidity'] . '</h6>';
        echo '<h6 class="card-text">Min Temp: ' . $weatherData['list'][$i]['main']['temp_min'] . '</h6>';
        echo '<h6 class="card-text">Max Temp: ' . $weatherData['list'][$i]['main']['temp_max'] . '</h6>';
        echo '</div></div></div>';
    }

    echo '</div>';
    echo '<footer class="text-center bottom">';
    echo '<h5><b>© Anika Tabassum 2023. All rights reserved.</b></h5>';
    echo '<div class="container d-flex gap-2 justify-content-center" style="width: 20%;">';
    echo '<a href="https://www.linkedin.com/in/anikatabassumm" target="_blank"></a>';
    echo '<a href="https://github.com/AnikaTabassumm" target="_blank"></a>';
    echo '</div>';
    echo '</footer>';
    echo '</div>';
    echo '</body>';
    echo '</html>';
}
?>
