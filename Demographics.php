	<?php
    
	//Get the zip-code of the Geographic location//
    
        $index=0;
		$zipcode='37745';
	
	//Creating Directory//
	
	    $dir='US Census 2010/'.$zipcode;
		if(!is_dir($dir))
		{
			mkdir('US Census 2010/'.$zipcode);
			mkdir('US Census 2010/'.$zipcode.'/Population');
			
		}
		$jsonResult=getDemographicsDataByZipcode($zipcode);
		$result=json_decode($jsonResult,true);
		$totalResult=count($result['d']);
		location_header($result['d'][0],$zipcode);
        populationByColor_header($result['d'][0],$zipcode);
		populationByRace_header($result['d'][0],$zipcode);
		populationBySex_header($result['d'][0],$zipcode);
		populationByAgeGroup_header($result['d'][0],$zipcode);
		populationByAgeGroupMale_header($result['d'][0],$zipcode);
		populationByAgeGroupFemale_header($result['d'][0],$zipcode);
		MedianAge_header($result['d'][0],$zipcode);
		Income_header($result['d'][0],$zipcode);
		HouseHold_Family_header($result['d'][0],$zipcode);
		Education_header($result['d'][0],$zipcode);
		Housing_header($result['d'][0],$zipcode);
		while($index<=($totalResult-1))
		{
		$array=convertMultiDimJsonToAssoc($result['d'][$index]);
		location($array,$zipcode);
		populationByColor($array,$zipcode);
		populationByRace($array,$zipcode);
		populationBySex($array,$zipcode);
		populationByAgeGroup($array,$zipcode);
		populationByAgeGroupMale($array,$zipcode);
		populationByAgeGroupFemale($array,$zipcode);
		MedianAge($array,$zipcode);
		Income($array,$zipcode);
		HouseHold_Family($array,$zipcode);
		Education($array,$zipcode);
		Housing($array,$zipcode);
		$index++;
		}
		
		
    //Convert the json result into one single array//
	    
		function convertMultiDimJsonToAssoc($array, $prefix = '') {
		  
		  $result = array();
		  
			foreach($array as $key=>$value) {
			 
			   if(is_array($value)) {
				$result = $result + convertMultiDimJsonToAssoc($value, $prefix . $key . '.');
			   }
			   else
				$result[$prefix.$key] = $value;
			}
		  return $result;
		}


	// Make the Geodata Service API call //

		function getDemographicsDataByZipcode($zipcode)
		{
		   $URL='https://azure.geodataservice.net/GeoDataService.svc/GetUSDemographics?zipcode='. $zipcode .'&$format=json';
		   $result=file_get_contents($URL);
		   return $result;
		}
	
	//  function for creating header of the location.csv file  //
	
	    function location_header($array,$zipcode)
		{
			$header=array();
			foreach($array as $key => $value)
			{
			    if(strtolower($key)=='zipcode'||strtolower($key)=='latitude'||strtolower($key)=='longitude'||$key=='StateAbbreviation'||$key=='State'||$key=='CityAbbreviation'||$key=='AreaCode'||$key=='City'||$key=='County'||$key=='CountyFIPSCode'||$key=='TimeZone'||$key=='StateFIPSCode'||$key=='USRegion'||$key=='USDivision')
				   array_push($header,$key);
			}
			$location_header=array($header);
			$fp = fopen('US Census 2010/'.$zipcode.'/location.csv', 'w');
			foreach($location_header as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
		
		}
		
	// function for writing the location data into the location.csv file //
	
	    function location($array,$zipcode)
	    {
	      
			$location=array(array($array['ZipCode'],$array['Latitude'],$array['Longitude'],$array['StateAbbreviation'],$array['State'],$array['CityAbbreviation'],$array['AreaCode'],$array['City'],$array['County'],$array['CountyFIPSCode'],$array['StateFIPSCode'],$array['TimeZone'],$array['USRegion'],$array['USDivision']));
			$fp = fopen('US Census 2010/'.$zipcode.'/location.csv', 'a');
			foreach($location as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
	   
	    }
		
		//  function for creating header of the populationByColor.csv file  //
		
		function populationByColor_header($array,$zipcode)
		{
		
		    $header=array();
			foreach($array as $key => $value)
			{
			    if(strtolower($key)=='population'||strtolower($key)=='whitepopulation'||strtolower($key)=='blackpopulation')
				   array_push($header,$key);
			}
			$population_header=array($header);
			$fp = fopen('US Census 2010/'.$zipcode.'/population/populationByColor.csv', 'w');
			foreach($population_header as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
			
		}
		
		// function for writing the  data into the populationByColor.csv file //
		
		function populationByColor($array,$zipcode)
		{
		
			$population=array(array($array['Population'],$array['WhitePopulation'],$array['BlackPopulation']));
			$fp = fopen('US Census 2010/'.$zipcode.'/population/populationByColor.csv', 'a');
			foreach($population as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
	   
		}
		
		//  function for creating header of the populationByRace.csv file  //
		
		function populationByRace_header($array,$zipcode)
		{
		
		    $header=array();
			foreach($array as $key => $value)
			{
			    if(strtolower($key)=='hispanicpopulation'||strtolower($key)=='asianpopulation'||strtolower($key)=='hawaiianpopulation'||strtolower($key)=='indianpopulation'||strtolower($key)=='otherpopulation')
				   array_push($header,$key);
			}
			$population_header=array($header);
			$fp = fopen('US Census 2010/'.$zipcode.'/population/populationByRace.csv', 'w');
			foreach($population_header as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
			
		}
		
		// function for writing the  data into the populationByRace.csv file //
		
		function populationByRace($array,$zipcode)
		{
		
			$population=array(array($array['HispanicPopulation'],$array['AsianPopulation'],$array['HawaiianPopulation'],$array['IndianPopulation'],$array['OtherPopulation']));
			$fp = fopen('US Census 2010/'.$zipcode.'/population/populationByRace.csv', 'a');
			foreach($population as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
	   
		}
		
		//  function for creating header of the populationBySex.csv file  //
		
		function populationBySex_header($array,$zipcode)
		{
		
		    $header=array();
			foreach($array as $key => $value)
			{
			    if(strtolower($key)=='malepopulation'||strtolower($key)=='femalepopulation')
				   array_push($header,$key);
			}
			$population_header=array($header);
			$fp = fopen('US Census 2010/'.$zipcode.'/population/populationBySex.csv', 'w');
			foreach($population_header as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
			
		}
		
		// function for writing the  data into the populationByRace.csv file //
		
		function populationBySex($array,$zipcode)
		{
		
			$population=array(array($array['MalePopulation'],$array['FemalePopulation']));
			$fp = fopen('US Census 2010/'.$zipcode.'/population/populationBySex.csv', 'a');
			foreach($population as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
	   
		}
		
		//  function for creating header of the MedianAge.csv file  //
		
		function MedianAge_header($array,$zipcode)
		{
		
		    $header=array();
			foreach($array as $key => $value)
			{
			    if(strtolower($key)=='medianage'||strtolower($key)=='medianagemale'||strtolower($key)=='medianagefemale')
				   array_push($header,$key);
			}
			$MedianAge_header=array($header);
			$fp = fopen('US Census 2010/'.$zipcode.'/MedianAge.csv', 'w');
			foreach($MedianAge_header as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
			
		}
		
		// function for writing the  data into the MedianAge.csv file //
		
		function MedianAge($array,$zipcode)
		{
		  
			$MedianAge=array(array($array['MedianAge'],$array['MedianAgeMale'],$array['MedianAgeFemale']));
			$fp = fopen('US Census 2010/'.$zipcode.'/MedianAge.csv', 'a');
			foreach($MedianAge as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
	   
		}
		
		//  function for creating header of the Income.csv file  //
		
		function Income_header($array,$zipcode)
		{
		
		    $header=array();
			foreach($array as $key1 => $value1)
			{
			   
				$pos = strpos($key1,'Income');
				if ($pos !== false) {
				   
				    array_push($header,$key1);
				
				
				}
			}
			$income_header=array($header);
			$fp = fopen('US Census 2010/'.$zipcode.'/Income.csv', 'w');
			foreach($income_header as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
			
		}
	
	    // function for writing the  data into the Income.csv file //
		
		function Income($array,$zipcode)
		{
		    $values=array();
			foreach($array as $key => $value)
			{
			   
				$pos = strpos($key,'Income');
				if ($pos !== false) {
				    
				    
					array_push($values,$array[$key]);
				
				
				}
			}
		    $income=array($values);
			$fp = fopen('US Census 2010/'.$zipcode.'/Income.csv', 'a');
			 foreach($income as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}  
			fclose($fp);
	   
		}
		
		//  function for creating header of the HouseHold_Family.csv file  //
		
		function HouseHold_Family_header($array,$zipcode)
		{
		
		    $header=array();
			foreach($array as $key => $value)
			{
			    if($key=='HouseholdsPerZipCode'||$key=='PersonsPerHousehold'||$key=='AverageHouseValue'||$key=='HouseholdsWithIndividualsUnder18'||$key=='HouseholdsWithIndividuals65plus')
				   array_push($header,$key);
			}
			foreach($array as $key1 => $value1)
			{
			   
				$pos = strpos($key1,'Family');
				if ($pos !== false) {
				   
				    array_push($header,$key1);
				
				
				}
			}
			$HouseHold_Family_header=array($header);
			$fp = fopen('US Census 2010/'.$zipcode.'/HouseHold_Family.csv', 'w');
			foreach($HouseHold_Family_header as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
			
		}
		
		// function for writing the  data into the HouseHold_Family.csv file //
		
		function HouseHold_Family($array,$zipcode)
		{
		    $values=array();
			$values=array($array['HouseholdsPerZipCode'],$array['PersonsPerHousehold'],$array['AverageHouseValue'],$array['HouseholdsWithIndividualsUnder18'],$array['HouseholdsWithIndividuals65plus']);
			foreach($array as $key => $value)
			{
			   
				$pos = strpos($key,'Family');
				if ($pos !== false) {
				    
				    
					array_push($values,$array[$key]);
				
				
				}
			}
		    $HouseHold_Family=array($values);
			$fp = fopen('US Census 2010/'.$zipcode.'/HouseHold_Family.csv', 'a');
			 foreach($HouseHold_Family as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}  
			fclose($fp);
	   
		}
		
		//function for creating header of the Education.csv file  //
		
		function Education_header($array,$zipcode)
		{
		
		    $header=array();
			foreach($array as $key => $value)
			{
			    if($key=='EducationHighSchoolGraduate'||$key=='EducationBachelorOrGreater')
				   array_push($header,$key);
			}
			$Education_header=array($header);
			$fp = fopen('US Census 2010/'.$zipcode.'/Education.csv', 'w');
			foreach($Education_header as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
			
		}
		
		// function for writing the  data into the Education.csv file //
		
		function Education($array,$zipcode)
		{
		  
			$Education=array(array($array['EducationHighSchoolGraduate'],$array['EducationBachelorOrGreater']));
			$fp = fopen('US Census 2010/'.$zipcode.'/Education.csv', 'a');
			foreach($Education as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
	   
		}
		
		//function for creating header of the populationByAgeGroup.csv file  //
		
		function populationByAgeGroup_header($array,$zipcode)
		{
		
		    $header=array();
			foreach($array as $key => $value)
			{
			    if($key=='PopulationMedian'||$key=='PopulationUnder5')
				   array_push($header,$key);
			}
			foreach($array as $key1 => $value1)
			{
			   
				$pos= strpos($key1,'PopulationMale');
				$pos1= strpos($key1,'PopulationFemale');
				if (($pos === false)&&($pos1 === false)){
				    $pos2= strpos($key1,'Population');
					if ($pos2 !== false)
					{
						$pos3= strpos($key1,'to');
						if ($pos3 !== false)
						{
				         array_push($header,$key1);
						}
					}
						 
				
				
				}
			}
			$population_header=array($header);
			$fp = fopen('US Census 2010/'.$zipcode.'/population/populationByAgeGroup.csv', 'w');
			foreach($population_header as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
			
		}
		
		// function for writing the  data into the populationByAgeGroup.csv file //
		
		function populationByAgeGroup($array,$zipcode)
		{
		    $values=array();
			$values=array($array['PopulationUnder5'],$array['PopulationMedian']);
			foreach($array as $key1 => $value1)
			{
			   
				$pos= strpos($key1,'PopulationMale');
				$pos1= strpos($key1,'PopulationFemale');
				if (($pos === false)&&($pos1 === false)){
				    $pos2= strpos($key1,'Population');
					if ($pos2 !== false)
					{
						$pos3= strpos($key1,'to');
						if ($pos3 !== false)
						{
				         array_push($values,$array[$key1]);
						}
					}
						 
				
				
				}
			}
		    $population=array($values);
			$fp = fopen('US Census 2010/'.$zipcode.'/population/populationByAgeGroup.csv', 'a');
			 foreach($population as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}  
			fclose($fp);
	   
		}
		
		//function for creating header of the populationByAgeGroupMale.csv file  //
		
		function populationByAgeGroupMale_header($array,$zipcode)
		{
		
		    $header=array();
			foreach($array as $key1 => $value1)
			{
			   
				$pos= strpos($key1,'PopulationMale');
				if ($pos !== false){
					
					array_push($header,$key1);
				}
			}
			$population_header=array($header);
			$fp = fopen('US Census 2010/'.$zipcode.'/population/populationByAgeGroupMale.csv', 'w');
			foreach($population_header as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
			
		}
		// function for writing the  data into the populationByAgeGroupMale.csv file //
		
		function populationByAgeGroupMale($array,$zipcode)
		{
		    $values=array();
			foreach($array as $key1 => $value1)
			{
			   
				$pos= strpos($key1,'PopulationMale');
				if ($pos !== false){
					
					array_push($values,$array[$key1]);
				}
			}
		    $population=array($values);
			$fp = fopen('US Census 2010/'.$zipcode.'/population/populationByAgeGroupMale.csv', 'a');
			 foreach($population as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}  
			fclose($fp);
	   
		}
		
		//function for creating header of the populationByAgeGroupFemale.csv file  //
		
		function populationByAgeGroupFemale_header($array,$zipcode)
		{
		
		    $header=array();
			foreach($array as $key1 => $value1)
			{
			   
				$pos= strpos($key1,'PopulationFemale');
				if ($pos !== false){
					
					array_push($header,$key1);
				}
			}
			$population_header=array($header);
			$fp = fopen('US Census 2010/'.$zipcode.'/population/populationByAgeGroupFemale.csv', 'w');
			foreach($population_header as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
			
		}
		
		// function for writing the  data into the populationByAgeGroupFemale.csv file //
		
		function populationByAgeGroupFemale($array,$zipcode)
		{
		    $values=array();
			foreach($array as $key1 => $value1)
			{
			   
				$pos= strpos($key1,'PopulationFemale');
				if ($pos !== false){
					
					array_push($values,$array[$key1]);
				}
			}
		    $population=array($values);
			$fp = fopen('US Census 2010/'.$zipcode.'/population/populationByAgeGroupFemale.csv', 'a');
			 foreach($population as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}  
			fclose($fp);
	   
		}
		
		//function for creating header of the Housing.csv file  //
		
		function Housing_header($array,$zipcode)
		{
		
		    $header=array();
			foreach($array as $key1 => $value1)
			{
			   
				$pos= strpos($key1,'Housing');
				if ($pos !== false){
					
					array_push($header,$key1);
				}
			}
			$Housing_header=array($header);
			$fp = fopen('US Census 2010/'.$zipcode.'/Housing.csv', 'w');
			foreach($Housing_header as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}
			fclose($fp);
			
		}
		
		// function for writing the  data into the Housing.csv file //
		
		function Housing($array,$zipcode)
		{
		    $values=array();
			foreach($array as $key1 => $value1)
			{
			   
				$pos= strpos($key1,'Housing');
				if ($pos !== false){
					
					array_push($values,$array[$key1]);
				}
			}
		    $Housing=array($values);
			$fp = fopen('US Census 2010/'.$zipcode.'/Housing.csv', 'a');
			 foreach($Housing as $fields)
			{
		       fputcsv($fp, $fields);
		  
		  
			}  
			fclose($fp);
	   
		}
		
		

    ?>