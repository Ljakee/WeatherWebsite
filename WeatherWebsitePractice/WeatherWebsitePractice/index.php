<html>
<head>
    <title>Jakes Super Weather Page</title>
</head>
<body>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />

    <?php

    echo 'Hello World!';
    $weatherURL = "http://api.open-meteo.com/v1/forecast?latitude=52.52&longitude=13.41&hourly=temperature_2m,relativehumidity_2m,rain,snow_depth,windspeed_10m,shortwave_radiation";
    $airqualityURL ="https://air-quality-api.open-meteo.com/v1/air-quality?latitude=52.5235&longitude=13.4115&hourly=pm10,pm2_5,dust,grass_pollen";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_URL, $weatherURL);

    //Gets our JSON result as a string
    $resultStr = curl_exec($ch);

    //Decode the JSON string into an object (which we can query)
    $result = json_decode($resultStr);

    //For our output while debugging - save the object back into json with "pretty" formatting
    $json_string = json_encode($result, JSON_PRETTY_PRINT);

    //Close the connection so we don't leave open ports on the PHP server
    wcurl_close($ch) ;

    $times = $result->hourly->time;
    $temps = $result->hourly->temperature_2m;
    $humidity = $result->hourly->relativehumidity_2m;
    $rain = $result->hourly->rain;
    $snow = $result->hourly->snow_depth;
    $wind = $result->hourly->windspeed_10m;
    $rads = $result->hourly->shortwave_radiation;
    $itemCount = count($result->hourly->time);

    ?>

    <div class="container">
        <div class="row" style="max-height:25vh">
            <canvas id="myChart"></canvas>
        </div>
        <div class="row" style="max-height:25vh">
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
