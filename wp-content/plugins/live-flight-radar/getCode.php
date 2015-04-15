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

	 
function get_data($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
$term = getuserinput($_GET["term"]);
$url = "http://airportcode.riobard.com/search?q=".$term."&fmt=JSON";
echo get_data($url)
?>