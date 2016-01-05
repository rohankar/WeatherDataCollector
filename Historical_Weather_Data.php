<?php

//date_default_timezone_set('Asia/Kolkata');
set_time_limit(0);
error_reporting(0);
$No_Past_year = 2;
$Past_year    = array();
$Count_year   = 0;
$lat          = '40.7903';
$long         = '73.9597';
$station_id;
$Start_timestamp;
$End_timestamp;

date_default_timezone_set(Find_Local_Timezone($lat, $long));

/*
 * Creating Directory 
 */

if (!is_dir($lat . ',' . $long)) {
    mkdir($lat . ',' . $long);
}

if (!is_dir($lat . ',' . $long . '/Historical Data')) {
    mkdir($lat . ',' . $long . '/Historical Data');
}


/* 
 * Generating the past years based on the current year and the no. of past year provided
 */

while ($Count_year <= $No_Past_year) {
    
    $Year = Date('Y', strtotime('-' . $Count_year . 'year'));
    array_push($Past_year, $Year);
    
    if (!is_dir($lat . ',' . $long . '/Historical Data/' . $Year)) {
        mkdir($lat . ',' . $long . '/Historical Data/' . $Year);
    }
    
    $Count_year++;
}


Find_Weather_Station($lat, $long, $Past_year);

/* 
 * Finding the local timezone based on lat & long
 */
 
 
function Find_Local_Timezone($lat, $long)
{
	global $date;
	$URL		=	'https://maps.googleapis.com/maps/api/timezone/json?location='.$lat.','.$long.'&timestamp='.strtotime(Date('d-m-Y'));
	$jsonResult	=	file_get_contents($URL);
	$result		=	json_decode($jsonResult,true);
	$timeZone_Id=	$result['timeZoneId'];
	return $timeZone_Id;
	
}  



/* 
 * Finding the closest weather station based on lat & long
 */

function Find_Weather_Station($lat, $long, $Past_year)
{
    
    $URL        = 'http://api.openweathermap.org/data/2.5/station/find?lat=' . $lat . '&lon=' . $long . '&cnt=1';
    $jsonResult = file_get_contents($URL);
    $result     = json_decode($jsonResult, true);
    $station_id = $result[0]['station']['id'];
    Generate_Date_Range($station_id, $Past_year);
    
}


/*
 * Function for generating the Date range as Unix timestamp 
 */

function Generate_Date_Range($station_id, $Past_year)
{
    
    global $No_Past_year;
    $Current_Month = Date('m');
    $Current_Year  = Date('Y');
    $Last_Year     = Date('Y', strtotime('-' . $No_Past_year . 'years'));
    $Last_Month    = Date('m', strtotime('-' . $No_Past_year . 'years'));
    foreach ($Past_year as $year) {
        
        $Total_Month = 12;
        $Month       = 1;
        if ((int) $Last_Year == (int) $year) {
            
            $Month = (int) $Last_Month;
            
        }
        while ($Month <= $Total_Month) {
            
            if ($Month == (int) $Last_Month && $year == (int) $Last_Year) {
                $first_day       = Date('d', strtotime('-' . $No_Past_year . 'years'));
                $Start_timestamp = strtotime($first_day . '-' . $Month . '-' . $year);
                $last_day        = Date('d', strtotime('last day of' . $Month . 'month'));
                $End_timestamp   = strtotime($last_day . '-' . $Month . '-' . $year);
                $End_timestamp   = strtotime('+1 day', $End_timestamp);
             
            }
                      
            else if ($Month == (int) $Current_Month && $year == (int) $Current_Year) {
			
                $last_day        = Date('d');
                $End_timestamp   = strtotime($last_day . '-' . $Month . '-' . $year);
                $End_timestamp   = strtotime('+1 day', $End_timestamp);
                $first_day       = Date('d', strtotime('first day of' . $Month . 'month'));
                $Start_timestamp = strtotime($first_day . '-' . $Month . '-' . $year);
                $Total_Month     = $Current_Month;
				
            } else if ($Month == 2 && isLeapYear($year)) {
                
                $End_timestamp   = strtotime('29-' . $Month . '-' . $year);
                $End_timestamp   = strtotime('+1 day', $End_timestamp);
                $first_day       = Date('d', strtotime('first day of' . $Month . 'month'));
                $Start_timestamp = strtotime($first_day . '-' . $Month . '-' . $year);
				
			} else {
			
                $last_day        = Date('d', strtotime('last day of' . $Month . 'month'));
                $End_timestamp   = strtotime($last_day . '-' . $Month . '-' . $year);
                $End_timestamp   = strtotime('+1 day', $End_timestamp);
                $first_day       = Date('d', strtotime('first day of' . $Month . 'month'));
                $Start_timestamp = strtotime($first_day . '-' . $Month . '-' . $year);
				
			}
			
            $jsonResult  = Fetch_Data($station_id, $Start_timestamp, $End_timestamp);
            $result      = json_decode($jsonResult, true);
            $totalResult = $result['cnt'];
            $count       = 0;
            Write_Weather_Header($year, $Month);
            while ($count < $totalResult) {
                $array = convertMultiDimJsonToAssoc($result['list'][$count]);
                Write_Weather_Data($array, $year, $Month);
                $count++;
            }
            $Month++;
        }
    }
    
    
}

/* Function for fetching the data */

function Fetch_Data($station_id, $Start_timestamp, $End_timestamp)
{
    
    $URL        = 'http://api.openweathermap.org/data/2.5/history/station?id=' . $station_id . '&type=hour&start=' . $Start_timestamp . '&end=' . $End_timestamp;
    $jsonResult = file_get_contents($URL);
    return $jsonResult;
    
    
}

/* Convert the json result into one single array */

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

function Write_Weather_Header($year, $Month)
{
    global $lat, $long;
    $Month          = Date('F', strtotime($Month . 'month'));
    $header         = array(
        'Date',
        'Temp(kelvin)',
        'Min_Temp',
        'Max_Temp',
        'Humidity(%)',
        'Min_Humidity',
        'Max_Humidity',
        'Dewpoint(kelvin)',
        'Min_Dewpoint',
        'Max_Dewpoint',
        'Wind_Speed(mps)',
        'Min_Wind_Speed',
        'Max_Wind_Speed',
        'Wind_Degree'
    );
    $Weather_Header = array(
        $header
    );
    $fp             = fopen($lat . ',' . $long . '/Historical Data/' . $year . '/' . $Month . '.csv', 'w');
    foreach ($Weather_Header as $fields) {
        fputcsv($fp, $fields);
        
    }
    
    fclose($fp);
    
}

/* Write Data into the csv file */

function Write_Weather_Data($array, $year, $Month)
{
    global $lat, $long;
	$Date    = Date('Y-m-d H:i:s:00 ', $array['dt']);
    $Month   = Date('F', strtotime($Month . 'month'));
    $values  = array(
        $Date,
        $array['temp.v'],
        $array['temp.mi'],
        $array['temp.ma'],
        $array['humidity.v'],
        $array['humidity.mi'],
        $array['humidity.ma'],
        $array['calc.dewpoint.v'],
        $array['calc.dewpoint.mi'],
        $array['calc.dewpoint.ma'],
        $array['wind.speed.v'],
        $array['wind.speed.mi'],
        $array['wind.speed.ma'],
        $array['wind.deg.v']
    );
    $Weather = array(
        $values
    );
    $fp      = fopen($lat . ',' . $long . '/Historical Data/' . $year . '/' . $Month . '.csv', 'a');
    foreach ($Weather as $fields) {
        fputcsv($fp, $fields);
        
    }
    
    fclose($fp);
}
function isLeapYear($year)
{
    return ((($year % 4 == 0) && ($year % 100)) || $year % 400 == 0) ? (true) : (false);
}


?>