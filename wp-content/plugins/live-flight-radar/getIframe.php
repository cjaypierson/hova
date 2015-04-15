<?php
		function getuserinput($input,$len=255, $nourl=1)
{
	 $input = str_replace('\n','<br />',$input);
	// $input = str_replace('strong','b',$input);
	 
	 $newString = substr($input,0,$len);
	 
	 $patterns[0] = "/--/";
	 $patterns[1] = "/\|/";
	 $patterns[2] = "/\$/";
	 $patterns[3] = "/\*/";
	 $patterns[4] = "/ or /";
	 $patterns[5] = "/SCRIPT/"; 
	 if ($nourl==1) {
		 $patterns[6] = "/http/"; 
		 $patterns[7] = "/ftp/"; 
		 $patterns[8] = "/www/"; 
	 }
 
	 $replacements[0] = '';
	 $replacements[1] = '\|';
	 $replacements[2] = '';
	 $replacements[3] = '';
	 $replacements[4] = '';
	 $replacements[5] = "s-c-r-i-p-t";
	  if ($nourl==1) {
		 $replacements[6] = '';
		 $replacements[7] = '';
		 $replacements[8] = '';
	  }	 


 
 	$newString = preg_replace($patterns, $replacements, $newString);
// echo "-----------------NEWSTRING: ".$newString;
	
 return $newString;

 
}


$code= $_GET["term"];
$xml = simplexml_load_file('http://api.flight.org/airports/xml/' . $code . '/airport.xml');
 if ($xml->status->attributes()->{'code'} == '200') {

		$lat = (string) $xml->data->data->attributes()->{'lat'};
		$lng = (string) $xml->data->data->attributes()->{'lng'};
		$dbValue = "$lat,$lng";

		   $zoom = '8';
   $width = '100%';
   $height = '350';    
echo '<iframe src="http://www.flightradar24.com/simple_index.php?lat=' . $lat . '&lon=' . $lng . '&z=' . $zoom . '" width="' . $width . '" height="' . $height . '"></iframe>';
}
?>