<?php 

/////////////////////////////////////////////
// IPv6 Services Monitoring Tool (IPV6-SMT)
//
// Author: Emre Yüce - ULAKBİM
// Contact: emre.yuce[@]tubitak.gov.tr
// Version: 0.1
// Release date: 15.08.2012
// License: GPL 2.0 or later
// Current file: init.php
// 
// Thanks to Murat Soysal - ULAKBİM, Onur Bektaş - ULAKBİM, Uğur Yılmaz - ULAKBİM
// 
/////////////////////////////////////////////

if (file_exists("config.json"))
{
	$config = file_get_contents("config.json"); 
	$config_arr = json_decode($config);
}
else 
	die("config.json file missing in script directory.");

# Set default timezone if it is not set by php.ini file
if(function_exists('date_default_timezone_set') AND (!ini_get('date.timezone')))
{
  date_default_timezone_set($config_arr->timezone);
}

//path to directory to scan
$directory = $config_arr->data_directory;

//get all files with a .json extension.
$files = glob($directory . "domains_*.json");
$files_count = glob($directory . "count_*.json");

//sort function for files
function sort_by_mtime($file1,$file2) 
{
	$time1 = filemtime($file1);
	$time2 = filemtime($file2);
	if ($time1 == $time2) 
	{
		return 0;
	}
	return ($time1 < $time2) ? 1 : -1;
}
// sort files array by modification time
usort($files,"sort_by_mtime");
usort($files_count,"sort_by_mtime");

$temp = explode('/',$files[0]);
$temp1 = explode('.',$temp[sizeof($temp)-1]);
$temp2 = explode('_',$temp1[0]);

if($_GET['req_date']=="")
{
	$req_date = $temp2[1];
}
else
{
	$req_date = $_GET['req_date'];
}

if (!(in_array($directory.'domains_'.$req_date.'.json', $files, true)) || !(in_array($directory.'count_'.$req_date.'.json', $files_count, true))) {
	echo "Date error!\n";
	exit;
}

//Read the requested date's data file and construct the data array
$string=file_get_contents($directory.'domains_'.$req_date.'.json');
$string_count=file_get_contents($directory.'count_'.$req_date.'.json');

$domain_arr=json_decode($string);
$count_arr=json_decode($string_count);


	?>
