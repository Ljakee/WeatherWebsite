<html>
<head>
    <title>Jakes Super Weather Page</title>
</head>
<body>


<h1>
AlexWeather.com

</h1>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Manrope&display=swap" rel="stylesheet" />
    <div class="container">
        <?php

    class JakesAPIAccess {

        public $lat;
        public $long;

        function __construct($lat, $long) {
            $this->lat = $lat;
            $this->long = $long;
        }

        function getCurl($url) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);

            //Gets our JSON result as a string
            $resultStr = curl_exec($ch);

            //Decode the JSON string into an object (which we can query)
            $result = json_decode($resultStr);

            //Close the connection so we don't leave open ports on the PHP server
            curl_close($ch);

            return $result;
        }

        function getWeatherData() {

            $weatherURL = "http://api.open-meteo.com/v1/forecast?latitude={$this->lat}&longitude={$this->long}&hourly=temperature_2m,relativehumidity_2m,rain,snow_depth,windspeed_10m,shortwave_radiation";
            return $this->getCurl($weatherURL);

        }

        function getAirQualityData() {

            $airqualityURL ="http://air-quality-api.open-meteo.com/v1/air-quality?latitude={$this->lat}&longitude={$this->long}&hourly=pm10,pm2_5,dust,grass_pollen";
            return $this->getCurl($airqualityURL);
        }

    }

    //Berlin
    $lat = 52.5235;
    $long = 13.4115;

    //EPIC (Paignton)
    $lat = 50.41360099030512;
    $long = -3.5788104274362027;

    //Puente de los Pilones (A rickety old bridge in Spain)
    $lat = 40.11694098852221;
    $long = -4.656482911578309;

    $jakieForTheWin = new JakesAPIAccess($lat, $long);

    $weatherData = $jakieForTheWin->getWeatherData();
    $airQualityData = $jakieForTheWin->getAirQualityData();

    $times = $weatherData->hourly->time;
    $temps = $weatherData->hourly->temperature_2m;
    $humidity = $weatherData->hourly->relativehumidity_2m;
    $rain = $weatherData->hourly->rain;
    $snow = $weatherData->hourly->snow_depth;
    $wind = $weatherData->hourly->windspeed_10m;
    $rads = $weatherData->hourly->shortwave_radiation;
    $pollen = $airQualityData->hourly->grass_pollen;
    $itemCount = count($weatherData->hourly->time);

        ?>

        <div class="container">
            <div class="row" style="max-height:50vh">
                <canvas id="myChart"></canvas>
            </div>
            <div class="row" style="max-height:50vh">
                <div class="col">
                    <canvas id="rainOnly"></canvas>
                </div>
                <div class="col">
                    <canvas id="windOnly"></canvas>
                </div>
            </div>
        </div>

        <div class="container" style="max-height:50vh; overflow-y:auto">
            <table class="table table-striped table-dark">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Temp</th>
                        <th>Pollen</th>
                        <th>Humidity</th>
                        <th>Rain</th>
                        <th>Snow Depth</th>
                        <th>Wind</th>
                        <th>Shortwave Radiation?!</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                for ($i = 1; $i <= $itemCount; $i++) {
                    $html =  "
                        <tr>
                            <td>{$times[$i]}</td>
                            <td>{$temps[$i]}</td>
                            <td>{$pollen[$i]}</td>
                            <td>{$humidity[$i]}</td>
                            <td>{$rain[$i]}</td>
                            <td>{$snow[$i]}</td>
                            <td>{$wind[$i]}</td>
                            <td>{$rads[$i]}</td>
                        </tr>
                    ";
                    echo $html;
                }

                    ?>
                </tbody>
            </table>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>

    //This is javascript - lets get funky!

                const data = {
          labels: <?php echo json_encode($times) ?>,
          datasets: [
            {
              label: 'Rain',
              data: <?php echo json_encode($rain) ?>,
              borderColor: 'blue',
              backgroundColor: 'blue',
            },
            {
              label: 'Wind',
              data: <?php echo json_encode($wind) ?>,
              borderColor: 'gray',
              backgroundColor: 'gray',
            }
          ]
        };

        const config = {
          type: 'line',
          data: data,
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'top',
              },
              title: {
                display: true,
                text: 'Wind & Rain Brotha!'
              }
            }
          },
        };

                const rainData = {
          labels: <?php echo json_encode($times) ?>,
          datasets: [
            {
              label: 'Rain',
              data: <?php echo json_encode($rain) ?>,
              borderColor: 'blue',
              backgroundColor: 'blue',
            }
          ]
        };

        const rainConfig = {
          type: 'bar',
          data: rainData,
          options: {
            responsive: true,
            plugins: {
              legend: null,
              title: {
                display: true,
                text: 'It\'s gonna rain!'
              }
            }
          },
        };

        const windData = {
          labels: <?php echo json_encode($times) ?>,
          datasets: [
            {
              label: 'Wind',
              data: <?php echo json_encode($wind) ?>,
              borderColor: 'grey',
              backgroundColor: 'grey',
            }
          ]
        };

        const windConfig = {
          type: 'bar',
          data: windData,
          options: {
            responsive: true,
            plugins: {
              legend: null,
              title: {
                display: true,
                text: 'It\'s windy!'
              }
            }
          },
        };

                    //Main chart (combined stuff)
            const ctx = document.getElementById('myChart');
        new Chart(ctx, config);

                //Rain chart
            const ctxR = document.getElementById('rainOnly');
        new Chart(ctxR, rainConfig);

                //Wind chart
            const ctxW = document.getElementById('windOnly');
            new Chart(ctxW, windConfig);



        </script>

</body>
</html>
