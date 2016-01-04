<?php

/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/*::                                                                         :*/
/*::  This routine calculates the distance between two points (given the     :*/
/*::  latitude/longitude of those points). It is being used to calculate     :*/
/*::  the distance between two locations using GeoDataSource(TM) Products    :*/
/*::                     													 :*/
/*::  Definitions:                                                           :*/
/*::    South latitudes are negative, east longitudes are positive           :*/
/*::                                                                         :*/
/*::  Passed to function:                                                    :*/
/*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
/*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
/*::    unit = the unit you desire for results                               :*/
/*::           where: 'M' is statute miles                                   :*/
/*::                  'K' is kilometers (default)                            :*/
/*::                  'N' is nautical miles                                  :*/
/*::  Worldwide cities and other features databases with latitude longitude  :*/
/*::  are available at http://www.geodatasource.com                          :*/
/*::                                                                         :*/
/*::  For enquiries, please contact sales@geodatasource.com                  :*/
/*::                                                                         :*/
/*::  Official Web site: http://www.geodatasource.com                        :*/
/*::                                                                         :*/
/*::         GeoDataSource.com (C) All Rights Reserved 2014		   		     :*/
/*::                                                                         :*/
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
function getGeoDistance($lat1, $lon1, $lat2, $lon2, $unit) {

  //echo "lat2 : " , $lat2 . " long2: " . $lon2 . "<br/>";
  
  $lat2 = (double)$lat2;
  $lon2 = (double)$lon2;
  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
  } else {
      return $miles;
  }
}


function getDistance($latitude1, $longitude1, $latitude2, $longitude2, $unit) {
	$earth_radius = 6371; // In KM
	
	$dLat = deg2rad($latitude2 - $latitude1);
	$dLon = deg2rad($longitude2 - $longitude1);
	
	$a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
	$c = 2 * asin(sqrt($a));
	$d = $earth_radius * $c;
	
      if ($unit == "M") {
        return ($d / 1.609344);
      } else if ($unit == "N") {
          return ( ($d / 1.609344) * 0.8684);
      } else {
          return $d;
  }
}


$baseLatitude = 44.779901; 
$baseLongitude = -93.508901;
$radius = 7;
getStationsWithinRadius($baseLatitude,$baseLongitude,$radius,"newStations1.csv");


function getColumnIndexByName($fileName, $columnName){
     
     $fileHandle = fopen($fileName, 'r');      
     $line = fgets($fileHandle);      
     $fields = explode(',', $line);
     
     /* Strip leading and trailing space from column heads */
     while ( list($index, $value) = each($fields) ){
        $cleanedFields[$index] = trim($value);
     }
     
     /* Convert to lower cases to avoid conflict in column names */
     $index = array_search(strtolower($columnName), array_map('strtolower', $cleanedFields));
     
     fclose($fileHandle);      
     return $index;
  }
   
function getStationsWithinRadius($baseLatitude, $baseLongitude, $radiusInMiles, $fileNameToWrite){
  
  // Shakopee Supervalu Store Lat Long
  // Got From Google Map, Using Store Location,
  $rowNum = 0;
  $stationIDFileName = "Minesotacleaned.csv";
  $stationNamesWithinRadius = $fileNameToWrite; 
  
  $stationFileHandle = fopen($stationIDFileName, "r");
  
  $zipIndex = getColumnIndexByName($stationIDFileName, "Zip Code");
  $latIndex = getColumnIndexByName($stationIDFileName, "latitude");
  $longIndex = getColumnIndexByName($stationIDFileName, "longitude");
  $cityIndex = getColumnIndexByName($stationIDFileName, "city");
  $stateIndex = getColumnIndexByName($stationIDFileName, "state");
  
  
  $stationIDFileHeader[0] = "Zipcode";
  $stationIDFileHeader[1] = "Latitude";
  $stationIDFileHeader[2] = "Longitude";
  $stationIDFileHeader[3] = "City";
  $stationIDFileHeader[4] = "State";
  $stationIDFileHeader[5] = "Distance";
  
  writeParametersToFile($stationNamesWithinRadius, $stationIDFileHeader);
  
  while($line = fgets($stationFileHandle)) {
    
    $rowNum++;
    $fields = explode(',', $line);
    
    $zipCode = $fields[$zipIndex];
    $lat = $fields[$latIndex];
    $long = $fields[$longIndex];
    $cityName = $fields[$cityIndex];
    $stateName = str_replace("\n", "", $fields[$stateIndex]);
    
    $distance = getGeoDistance($baseLatitude, $baseLongitude, $lat, $long, "M");
    
    $stationData[0] = $zipCode;
    $stationData[1] = $lat;
    $stationData[2] = $long;
    $stationData[3] = $cityName;
    $stationData[4] = $stateName;
    $stationData[5] = $distance;
    
    if ($distance <= $radiusInMiles)
      writeParametersToFile($stationNamesWithinRadius, $stationData);
    
  }
  
  fclose($stationFileHandle);  
}

function writeParametersToFile($fileNameToWrite, $data){
  
  $filehandle = fopen($fileNameToWrite, "a");
  
  for($i = 0; $i < count($data) - 1 ; $i++){
    fwrite($filehandle, $data[$i] . ","); 
  }
  
  fwrite($filehandle, $data[$i] . "\n");
  fclose($filehandle);
}



echo "<br><br>";
echo getGeoDistance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
echo getGeoDistance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
echo getGeoDistance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";

echo getGeoDistance(44.783482,  -93.533852,  44.760216,  -93.502731, "M") . " Miles<br>";
echo getGeoDistance(44.783482,  -93.533852,  44.81,  -93.63, "M") . " Miles -Kilometers<br>";
echo getGeoDistance(44.783482,  -93.533852,  44.760216,  -93.502731, "N") . " Nautical Miles<br><br>";

// ---

echo "Using New Formula <br><br>";

echo getDistance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
echo getDistance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
echo getDistance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";

echo getDistance(44.783482,  -93.533852,  44.760216,  -93.502731, "M") . " Miles<br>";
echo getDistance(44.783482,  -93.533852,  44.81,  -93.63, "M") . " Miles -Kilometers<br>";
echo getDistance(44.783482,  -93.533852,  44.760216,  -93.502731, "N") . " Nautical Miles<br>";


?>