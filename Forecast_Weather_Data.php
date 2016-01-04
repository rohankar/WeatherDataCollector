<?php

$lat  = '12.912619';
$long = '77.601519';
$station_id;
$station_long;
$station_lat;
$date;
$year;
$month;

Find_Local_Timezone($lat, $long);

/*
 * Creating Directory 
 */

if (!is_dir($lat . ',' . $long)) {
    mkdir($lat . ',' . $long);
}

if (!is_dir($lat . ',' . $long . '/Forecast Data')) {
    mkdir($lat . ',' . $long . '/Forecast Data');
}

if (!is_dir($lat . ',' . $long . '/Forecast Data/' . $year)) {
    mkdir($lat . ',' . $long . '/Forecast Data/' . $year);
}

if (!is_dir($lat . ',' . $long . '/Forecast Data/' . $year.'/'.$month)) {
    mkdir($lat . ',' . $long . '/Forecast Data/' . $year.'/'.$month);
}

if (!is_dir($lat . ',' . $long . '/Forecast Data/' . $year.'/'.$month.'/'.$date)) {
    mkdir($lat . ',' . $long . '/Forecast Data/' . $year.'/'.$month.'/'.$date);
}

Find_Weather_Station($lat, $long);



/* 
 * Finding the local timezone based on lat & long
 */
 
 
 function Find_Local_Timezone($lat, $long)
 {
	global $date,$year,$month;
	$URL='https://maps.googleapis.com/maps/api/timezone/json?location='.$lat.','.$long.'&timestamp='.strtotime(Date('d-m-Y'));
	$jsonResult=file_get_contents($URL);
	$result=json_decode($jsonResult,true);
	$timeZone_Id=$result['timeZoneId'];
	date_default_timezone_set($timeZone_Id);
	$date=Date('d-m-Y');
	$year=Date('Y');
	$month=Date('F');
 }



/* 
 * Finding the closest weather station based on lat & long
 */

function Find_Weather_Station($lat, $long)
{
    
    $URL          = 'http://api.openweathermap.org/data/2.5/station/find?lat=' . $lat . '&lon=' . $long . '&cnt=1';
    $jsonResult   = file_get_contents($URL);
    $result       = json_decode($jsonResult, true);
    $station_id   = $result[0]['station']['id'];
    $station_lat  = $result[0]['station']['coord']['lat'];
    $station_long = $result[0]['station']['coord']['lon'];
    Fetch_Weather_Data($station_lat, $station_long);
    
}

/* 
 * Fetch Forecast Data based on lat & long
 */

function Fetch_Weather_Data($station_lat, $station_long)
{
    
    $URL        = 'https://api.worldweatheronline.com/free/v2/weather.ashx?key=aa9967b205f647292c8eb466b7b8d&q=' . $station_lat . ',' . $station_long . '&num_of_days=5&tp=3&format=json&includeLocation=yes&cc=no';
    $jsonResult = file_get_contents($URL);
    $result     = json_decode($jsonResult, true);
	//print_r($result);
	$TotalResult= count($result['data']['weather']);
	$TotalCount=0;
	while($TotalCount<$TotalResult){
		$HourlyCount=0;
		$Forecast_date=$result['data']['weather'][$TotalCount]['date'];
		write_weather_header($Forecast_date);
		$TotalHourlyCount=count($result['data']['weather'][$TotalCount]['hourly']);
		while($HourlyCount<$TotalHourlyCount)
		{
			$array = convertMultiDimJsonToAssoc($result['data']['weather'][$TotalCount]['hourly'][$HourlyCount]);
			print_r($array);
			write_weather_data($array,$Forecast_date);
			$HourlyCount++;
		}
		$TotalCount++;
	}
   
}

/* 
 * Convert the json result into one single array
 */

function convertMultiDimJsonToAssoc($array, $prefix = '')
{
    
    $result = array();
    foreach ($array as $key => $value) {
        
        if (is_array($value)) {
            $result = $result + convertMultiDimJsonToAssoc($value, $prefix . $key . '.');
        } else
            $result[$prefix . $key] = $value;
    }
    
    return $result;
}

/* Create header for the csv file */

function write_weather_header($Forecast_date)
{
    global $lat, $long,$date,$month,$year;
    $header         = array(
		'Time',
        'chance of fog in %',
        'chance of frost in %',
        'chance of high temp in %',
		'chance of overcast in %',
        'chance of rain in %',
		'chance of snow in %',
		'chance of sunshine in %',
		'chance of thunder in %',
		'chance of windy in %',
		'cloud cover in %',
		'Dew Point in Celcius',
		'Temp in celcius',
        'Visibility in km',
        'Weather Description',
        'Pressure in millibars',
        'Humidity in %',
        'Precipitation in millimetres',
        'Wind direction in degree',
        'Wind Speed in kmph',
		'Wind Gust in kmph'
    );
    $Weather_Header = array(
        $header
    );
    $fp             = fopen($lat . ',' . $long . '/Forecast Data/' . $year.'/'.$month.'/'.$date.'/'.$Forecast_date. '.csv', 'w');
    foreach ($Weather_Header as $fields) {
        fputcsv($fp, $fields);
        
    }
    
    fclose($fp);
}

/* Write Data into the csv file */

function write_weather_data($array,$Forecast_date)
{
    $array['time']=(int)$array['time']/100 .':00';
    global $lat, $long,$date,$month,$year;
    $values       = array(
        $array['time'],
		$array['chanceoffog'],
		$array['chanceoffrost'],
		$array['chanceofhightemp'],
		$array['chanceofovercast'],
		$array['chanceofrain'],
		$array['chanceofsnow'],
		$array['chanceofsunshine'],
		$array['chanceofthunder'], 
		$array['chanceofwindy'],
        $array['cloudcover'],
		$array['DewPointC'],
		$array['tempC'],
		$array['visibility'],
        $array['weatherDesc.0.value'],
        $array['pressure'],
        $array['humidity'],
        $array['precipMM'],
        $array['winddirDegree'],
        $array['windspeedKmph'],
		$array['WindGustKmph']
    );
	//print_r($values);
    $Weather_Data = array(
        $values
    );
    $fp           = fopen($lat . ',' . $long . '/Forecast Data/' . $year.'/'.$month.'/'.$date.'/'.$Forecast_date.'.csv', 'a');
    foreach ($Weather_Data as $fields) {
        fputcsv($fp, $fields);
        
    }
    
    fclose($fp);
    
}

?>






















