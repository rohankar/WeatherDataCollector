<?php

date_default_timezone_set('Asia/Kolkata');
set_time_limit(0);
$lat  = '';
$long = '';
$station_id;
$station_long;
$station_lat;
$date;
$year;
$month;
$Next_Date;


Find_Local_Timezone($lat, $long);

/*
 * Creating Directory 
 */

if (!is_dir($lat . ',' . $long)) {
    mkdir($lat . ',' . $long);
}

if (!is_dir($lat . ',' . $long . '/Current Data')) {
    mkdir($lat . ',' . $long . '/Current Data');
}

if (!is_dir($lat . ',' . $long . '/Current Data/' . $year)) {
    mkdir($lat . ',' . $long . '/Current Data/' . $year);
}

if (!is_dir($lat . ',' . $long . '/Current Data/' . $year.'/'.$month)) {
    mkdir($lat . ',' . $long . '/Current Data/' . $year.'/'.$month);
}


Find_Weather_Station($lat, $long);

/* 
 * Finding the local timezone based on lat & long
 */
 
 
 function Find_Local_Timezone($lat, $long)
 {
	global $date,$year,$month,$Next_Date;
	$URL='https://maps.googleapis.com/maps/api/timezone/json?location='.$lat.','.$long.'&timestamp='.strtotime(Date('d-m-Y'));
	$jsonResult=file_get_contents($URL);
	$result=json_decode($jsonResult,true);
	$timeZone_Id=$result['timeZoneId'];
	date_default_timezone_set($timeZone_Id);
	$date=Date('d-m-Y');
	$year=Date('Y');
	$month=Date('F');
	$Next_Date=Date('d-m-Y',strtotime('+1 day'));
 }



/* 
 * Finding the closest weather station based on station's lat & long
 */

function Find_Weather_Station($lat, $long)
{
    
					/* Making API calls per minute for a day */
					
	$URL          = 'http://api.openweathermap.org/data/2.5/station/find?lat=' . $lat . '&lon=' . $long . '&cnt=1';
    $jsonResult   = file_get_contents($URL);
    $result       = json_decode($jsonResult, true);
    $station_id   = $result[0]['station']['id'];
    $station_lat  = $result[0]['station']['coord']['lat'];
    $station_long = $result[0]['station']['coord']['lon'];
	write_weather_header();
    Fetch_Weather_Data($station_lat, $station_long);
    
}

/* 
 * Fetch Current Data based on lat & long
 */

function Fetch_Weather_Data($station_lat, $station_long)
{
	global $date,$Next_Date;
	
	while(Strtotime($date) < strtotime($Next_Date))
	{
		$URL        = 'https://api.worldweatheronline.com/free/v2/weather.ashx?key=aa9967b205f647292c8eb466b7b8d&q=' . $station_lat . ',' . $station_long . '&num_of_days=5&tp=3&format=json&includeLocation=yes';
		$jsonResult = file_get_contents($URL);
		$result     = json_decode($jsonResult, true);
		$array = convertMultiDimJsonToAssoc($result['data']['current_condition'][0]);
		write_weather_data($array);
		sleep(60);
		$date=Date('d-m-Y');
		echo $date;
		if(strtotime($date)==strtotime($Next_Date))
			$Next_Date=Date('d-m-Y',strtotime('+1 day'));
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

function write_weather_header()
{
    global $date, $lat, $long,$year,$month;
    $header         = array(
        'Observation Time',
        'Temp in celcius',
        'Visibility in km',
        'Weather Description',
        'Pressure in millibars',
        'Cloud Cover in %',
        'Humidity in %',
        'Precipitation in millimetres',
        'Wind direction in degree',
        'Wind Speed in kmph'
    ); 
	/* $header = array('Observation Time','Sunrise','Sunset','Weather Description','Temp in kelvin','Temp_Max in kelvin','Temp_Min in kelvin','Pressure in hpa','Humidity in %','Wind Speed in mps','Wind Degree','Clouds in %'); */
    $Weather_Header = array(
        $header
    );
    $fp             = fopen($lat . ',' . $long . '/Current Data/' . $year.'/'.$month. '/' .$date. '.csv', 'w');
    foreach ($Weather_Header as $fields) {
        fputcsv($fp, $fields);
        
    }
    
    fclose($fp);
} 

/* Write Data into the csv file */

function write_weather_data($array)
{
   /*  $Obesrvation_Time=Date('h:i:sa',$array['dt']);
	$Sunrise_Time=Date('h:i:sa',$array['sys.sunrise']);
	$Sunset_Time=Date('h:i:sa',$array['sys.sunset']); */
    global $date,$lat,$long,$year,$month;
    $values       = array(
        $array['observation_time'],
        $array['temp_C'],
        $array['visibility'],
        $array['weatherDesc.0.value'],
        $array['pressure'],
        $array['cloudcover'],
        $array['humidity'],
        $array['precipMM'],
        $array['winddirDegree'],
        $array['windspeedKmph']
    ); 
	/* $values	=	array( $Obesrvation_Time,$Sunrise_Time,$Sunset_Time,$array['weather.0.description'],$array['main.temp'],$array['main.temp_max'],$array['main.temp_min'],$array['main.pressure'],$array['main.humidity'],$array['wind.speed'],$array['wind.deg'],$array['clouds.all']); */
    $Weather_Data = array(
        $values
    );
    $fp           = fopen($lat . ',' . $long . '/Current Data/' . $year.'/'.$month. '/' .$date. '.csv', 'a');
    foreach ($Weather_Data as $fields) {
        fputcsv($fp, $fields);
        
    }
    
    fclose($fp);
    
}

?>>