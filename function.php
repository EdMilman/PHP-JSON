<!DOCTYPE html>
<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
</head>
<body>
<?php
// loads data from html into 'sets' to remove duplicates
$all_countries = array_unique(array($_GET["c1"], $_GET["c2"], $_GET["c3"], $_GET["c4"]));

$all_properties = array_unique(array($_GET["p1"], $_GET["p2"], $_GET["p3"], $_GET["p4"], $_GET["p5"]));

// loads key value pairs to translate codes from html page into user friendly text
$decode_country = array("uk" => "UK", "gm" => "Germany", "it" => "Italy", "fr" => "France");

$property_decode = array("pop" => "Population", "area" => "Area", "dep" => "Dependency Ratio",
    "pgr" => "Population Growth Rate", "age" => "Median Age", "sex" => "Sex Ratio (at birth)",
    "life" => "Life Expectancy at Birth", "he" => "Health Expenditures", "gdp" => "GDP", "ele" => "Electricity Production",
);

// empty array to hold data
$data = array();

// fill array with data for all countries selected
foreach ($all_countries as $country) {
    $data[] = json_decode(file_get_contents("$country.json"), true);
}

// create final string to be printed
$str = "<table border='1'><th>Property</th>";

// counter to reference country in graphs
$count = 0;
// loop to create table headers
foreach ($all_countries as $country) {
    $str .= "<th>Country $count:</br>$decode_country[$country]</th>";
    $country_name[] = $decode_country[$country];
    $count++;
}

// array to hold graph data
$graph = array();

// loop through each country and property, appending relevant section from data array to final string
foreach ($all_properties as $property) {
    $str .= "<tr><td>$property_decode[$property]</td>";
    $country_name[] = "$property_decode[$property]";
    foreach ($data as $data_item) {
        switch ($property) {
            case "pop":
                $temp = preg_replace("/(?![.=$'€%-])\p{P}/u", "", substr($data_item["People and Society"]["Population"]["text"], 0, 11));
                // add data to graph array
                $graph[] = $temp;
                $str .= "<td>$temp</td>";
                break;
            case "area":
                $temp = $data_item["Geography"]["Area"]["total"]["text"];
                $str .= "<td>$temp</td>";
                break;
            case "dep":
                $temp = $data_item["People and Society"]["Dependency ratios"]["total dependency ratio"]["text"];
                $str .= "<td>$temp</td>";
                break;
            case "age":
                $temp = $data_item["People and Society"]["Median age"]["total"]["text"];
                $str .= "<td>$temp</td>";
                break;
            case "pgr":
                $temp = $data_item["People and Society"]["Population growth rate"]["text"];
                $str .= "<td>$temp</td>";
                break;
            case "sex":
                $temp = $data_item["People and Society"]["Sex ratio"]["at birth"]["text"];
                $str .= "<td>$temp</td>";
                break;
            case "life":
                $temp = $data_item["People and Society"]["Life expectancy at birth"]["total population"]["text"];
                $str .= "<td>$temp</td>";
                break;
            case "he":
                $temp = $data_item["People and Society"]["Health expenditures"]["text"];
                $str .= "<td>$temp</td>";
                break;
            case "gdp":
                $temp = $data_item["Economy"]["GDP (official exchange rate)"]["text"];
                $str .= "<td>$temp</td>";
                break;
            case "ele":
                $temp = $data_item["Energy"]["Electricity - production"]["text"];
                $str .= "<td>$temp</td>";
                break;
        }
    }
    $str .= "</tr>";
}
// print final string
print $str .= "</table>";
?>
<button type="button" onclick=window.history.back()>Back</button>
<canvas id="myChart" width="0" height="0"></canvas>
<script>
    // load data from php graph array into javascript array for use in table
    var pop_data = [<?php foreach ($graph as $item) {
        print "$item,";
    }
        ?>];

    // create table only if population is chosen
    if(pop_data.length > 0){
        var myChart = new Chart(document.getElementById("myChart"), {
            type: 'bar',
            data: {
                labels: Array.apply(null, {length: pop_data.length}).map(Number.call, Number),
                datasets: [{
                    label: 'Population',
                    data: pop_data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        })
    }
</script>
</body>
</html>