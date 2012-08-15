<?php
/////////////////////////////////////////////
// IPv6 Services Monitoring Tool (IPV6-SMT)
//
// Author: Emre Yüce - ULAKBİM
// Contact: emre.yuce[@]tubitak.gov.tr
// Version: 0.1
// Release date: 15.08.2012
// License: GPL 2.0 or later
// Current file: data_collector.php
// 
// Thanks to Murat Soysal - ULAKBİM, Onur Bektaş - ULAKBİM, Uğur Yılmaz - ULAKBİM
// 
/////////////////////////////////////////////

# Script should be run only from command line.
if (php_sapi_name() != 'cli')
{
	die("This script can only be run over CLI!");
}

# Check for config.json in the same directory.
if (file_exists("config.json"))
{
	$config = file_get_contents("config.json"); 
	$config_arr = json_decode($config);
}
else 
	die("\nERROR: config.json file missing in script directory.\n");
	
if ($config_arr->debug)
{
	echo "config.json...OK!\n";
}


$i=0;
$domain_arr[] = array(
			'id'=> '',
			'name' => '', 
			'domain' => '',
			'domain_www' => '',
			'IPv6_allocated' => '0', // 1 or 0
			'WWW'=>array(
				'HOST'=>'',
				'CNAME'=>array(
					'0'=>''
					),
				'A'=>array(
					'0'=>''
					),
				'IPv6_support'=>'0',
				'IPv6_check'=>'0',
				'AAAA'=>array(
					'0'=>'',
					)
				),
			'NS'=>array(
				'0'=>array(
					'HOST'=>'',
					'TARGET'=>'',
					'A'=>array(
						'0'=>''
						),
					'IPv6_support'=>'0',
					'IPv6_check'=>'0',
					'AAAA'=>array(
						'0'=>''
						)
					),
				),
			'MX'=>array(
				'0'=>array(
					'HOST'=>'',
					'TARGET'=>'',
					'A'=>array(
						'0'=>''
						),
					'IPv6_support'=>'0',
					'IPv6_check'=>'0',
					'AAAA'=>array(
						'0'=>''
						)
					),
				),
			'Count'=>array(
				
				'NS'=>array(
					'IPv6_check'=>0,
					'IPv6_support'=>0,
					'Total'=>0
					),
				'MX'=>array(
					'IPv6_check'=>0,
					'IPv6_support'=>0,
					'Total'=>0
					)
				)
				
				
		);
			
$Count_arr = array( 
		'Total' => 0, 
		'IPv6_prefix' => array('r'=>0,'y'=>0,'g'=>0), 
		'WWW' => array('r'=>0,'y'=>0,'g'=>0), 
		'NS' => array('r'=>0,'y'=>0,'g'=>0), 
		'MX' => array('r'=>0,'y'=>0,'g'=>0)
		);

# Reading input file
if ($config_arr->debug)
	{
		echo "Reading input.json...";
	}

if (file_exists($config_arr->input_file))
{
	$string = file_get_contents($config_arr->input_file);
	$inst_array = json_decode($string);	
			
	if ($config_arr->debug)
	{
		echo "OK!\n";
	}
}
else
{
	die("\nERROR: Could not read input file: ".$config_arr->input_file."\n");
}

# Parsing input file
foreach ( $inst_array as $inst_array_item) {

	if ($config_arr->debug)
	{
		echo "Parsing item ".$i.") ".$inst_array_item->inst_name."\n";
	}
	
	$domain_arr[$i]['id'] = $inst_array_item->inst_id;
	$domain_arr[$i]['name'] = $inst_array_item->inst_name;
	$domain_arr[$i]['domain'] = $inst_array_item->inst_domain;

	if ($inst_array_item->inst_domain_www !== '')
	{	
		$domain_arr[$i]['domain_www'] = $inst_array_item->inst_domain_www;
	}
	else
	{
		$domain_arr[$i]['domain_www'] = 'www.'.$inst_array_item->inst_domain;
	}

	$domain_arr[$i]['IPv6_allocated'] = $inst_array_item->inst_prefix;
	
	$domain = $domain_arr[$i]['domain'];
	$domain_www = $domain_arr[$i]['domain_www']; 
	
	$domain_arr[$i]['Count']['NS']['IPv6_check']=0;
	$domain_arr[$i]['Count']['NS']['IPv6_support']=0;
	$domain_arr[$i]['Count']['NS']['Total']=0;
	$domain_arr[$i]['Count']['MX']['IPv6_check']=0;
	$domain_arr[$i]['Count']['MX']['IPv6_support']=0;
	$domain_arr[$i]['Count']['MX']['Total']=0;

	$WWW_CNAME = dns_get_record($domain_www, DNS_CNAME);
	$WWW_A = dns_get_record($domain_www, DNS_A);
	$WWW_AAAA = dns_get_record($domain_www, DNS_AAAA);
	$res_NS = dns_get_record($domain, DNS_NS);
	$res_MX = dns_get_record($domain, DNS_MX);
	
	//IPv6 address allocation
	//Increase count for green or red
	if(is_numeric($domain_arr[$i]['IPv6_allocated']))
		$Count_arr['IPv6_prefix']['g']++;
	else
		$Count_arr['IPv6_prefix']['r']++;

	//WWW Info
	$j=0;
	foreach ($WWW_CNAME as $item) 
	{
		$domain_arr[$i]['WWW']['HOST'] = $item['host'];
		$domain_arr[$i]['WWW']['CNAME'][$j] = $item['target'];
		$j++;
	}
	
	$j=0;
	foreach ($WWW_A as $item) 
	{
		$domain_arr[$i]['WWW']['A'][$j] = $item['ip'];
		$j++;
	}
	$domain_arr[$i]['WWW']['IPv6_support'] = 0;
	foreach ($WWW_AAAA as $item) 
	{
		$domain_arr[$i]['WWW']['AAAA'][$j] = $item['ipv6'];
		$domain_arr[$i]['WWW']['IPv6_support'] = 1;
		$j++;
	}
	
	$domain_arr[$i]['WWW']['IPv6_check'] = 0;
	if ($domain_arr[$i]['WWW']['IPv6_support'])
	{
		//execute http check for $domain
		$result=array();
		exec($config_arr->nagios->check_http.' -H '.$domain_www.' -6', $result);
	
		//parse the result of check_http
		$pattern_WWW = '/^HTTP OK: HTTP\/1.1 200 OK/';
		if (preg_match($pattern_WWW, $result['0']))
		{
			$domain_arr[$i]['WWW']['IPv6_check'] = 1;
		}
	}
	
	if($domain_arr[$i]['WWW']['IPv6_support'] && $domain_arr[$i]['WWW']['IPv6_check'])
	{
		$Count_arr['WWW']['g']++;
	}
	elseif ($domain_arr[$i]['WWW']['IPv6_support'])
	{
		$Count_arr['WWW']['y']++;
	}
	else
	{
		$Count_arr['WWW']['r']++;
	}
				
				
	
	//NS Info
	$j=0; $jj=0;
	foreach ($res_NS as $item) 
	{
		$domain_arr[$i]['NS'][$j]['TARGET'] = $item['target'];
		
		$jj=0;
		$NS_A = dns_get_record($item['target'], DNS_A);
		foreach ($NS_A as $item2) 
		{
			$domain_arr[$i]['NS'][$j]['A'][$jj] = $item2['ip'];
			$jj++;
		}
		
		$jj=0;				
		$NS_AAAA = dns_get_record($item['target'], DNS_AAAA);
		$domain_arr[$i]['NS'][$j]['IPv6_support'] = 0;
		foreach ($NS_AAAA as $item3) 
		{
			$domain_arr[$i]['NS'][$j]['AAAA'][$jj] = $item3['ipv6'];
			$domain_arr[$i]['NS'][$j]['IPv6_support'] = 1;
			$domain_arr[$i]['Count']['NS']['IPv6_support']++;
			$jj++;
		}
		
		$domain_arr[$i]['NS'][$j]['IPv6_check'] = 0;
		if ($domain_arr[$i]['NS'][$j]['IPv6_support'])
		{
			//execute ns check for $domain
			$result=array();
			exec($config_arr->nagios->check_dns.' -H www.'.$domain.' -s '.$domain_arr[$i]['NS'][$j]['AAAA']['0'], $result);
	
			//parse the result of check_http
			$pattern_NS = '/^DNS OK: /';
			if (preg_match($pattern_NS, $result['0']))
			{
				$domain_arr[$i]['NS'][$j]['IPv6_check'] = 1;
				$domain_arr[$i]['Count']['NS']['IPv6_check']++;
			}
		}
		$domain_arr[$i]['Count']['NS']['Total']++;
		$j++;
	}
	
	switch ($domain_arr[$i]['Count']['NS']['IPv6_check']/$domain_arr[$i]['Count']['NS']['Total']) 
	{
		case 0:
			$Count_arr['NS']['r']++;
			break;
		case 1:
			$Count_arr['NS']['g']++;
			break;
		default:
			$Count_arr['NS']['y']++;
	}
	
	//MX Info
	$j=0; $jj=0;
	foreach ($res_MX as $item) 
	{
		$domain_arr[$i]['MX'][$j]['TARGET'] = $item['target'];
		//echo "<b>".$item['target']."</b><br />";
		
		$jj=0;
		$MX_A = dns_get_record($item['target'], DNS_A);
		foreach ($MX_A as $item2) 
		{
			$domain_arr[$i]['MX'][$j]['A'][$jj] = $item2['ip'];
			$jj++;
			//echo $item2['ip']."<br />";
		}
		
		$jj=0;				
		$MX_AAAA = dns_get_record($item['target'], DNS_AAAA);
		$domain_arr[$i]['MX'][$j]['IPv6_support'] = 0;
		foreach ($MX_AAAA as $item3) 
		{
			$domain_arr[$i]['MX'][$j]['AAAA'][$jj] = $item3['ipv6'];
			$domain_arr[$i]['MX'][$j]['IPv6_support'] = 1;
			$domain_arr[$i]['Count']['MX']['IPv6_support']++;
			$jj++;
		}
		
		$domain_arr[$i]['MX'][$j]['IPv6_check'] = 0;
		if ($domain_arr[$i]['MX'][$j]['IPv6_support'])
		{
	
			//execute ns check for $domain
			$result=array();
			exec($config_arr->nagios->check_smtp.' -H '.$domain_arr[$i]['MX'][$j]['TARGET'].' -6', $result);
			//print_r($result);
	
			//parse the result of check_http
			$pattern_MX = '/^SMTP OK - /';
			if (preg_match($pattern_MX, $result['0']))
			{
				$domain_arr[$i]['MX'][$j]['IPv6_check'] = 1;
				$domain_arr[$i]['Count']['MX']['IPv6_check']++;
			}
		}
		$domain_arr[$i]['Count']['MX']['Total']++;
		$j++;
	}
	
	switch ($domain_arr[$i]['Count']['MX']['IPv6_check']/$domain_arr[$i]['Count']['MX']['Total']) 
	{
		case 0:
			$Count_arr['MX']['r']++;
			break;
		case 1:
			$Count_arr['MX']['g']++;
			break;
		default:
			$Count_arr['MX']['y']++;
	}
	
	$Count_arr['Total']++;
	
	$i++;
}

if ($config_arr->debug)
{
	echo "Parsing completed.\n";
}

$today = date("d-m-Y");
$directory = $config_arr->data_directory;

if (is_dir($directory))
{
	if ($config_arr->debug)
	{
		echo "Writing details to file...";
	}
	$fp = fopen($directory.'domains_'.$today.'.json', 'w');
	if (fwrite($fp, json_encode($domain_arr)))
	{
		if ($config_arr->debug)
			echo "OK!\n";
	}
	else
		if ($config_arr->debug)
			die("ERROR: ".$directory."domains_".$today.".json is not writable.");
	fclose($fp);
	
	if ($config_arr->debug)
	{
		echo "Writing count to file...";
	}
	$fp = fopen($directory.'count_'.$today.'.json', 'w');
	if (fwrite($fp, json_encode($Count_arr)))
	{
		if ($config_arr->debug)
			echo "OK!\n";
	}
	else
		if ($config_arr->debug)
			die("ERROR: ".$directory."domains_".$today.".json is not writable.");
	fclose($fp);
}
else
	die("\nERROR: ".$directory." does not exist. Check data_directory value in config.json .\n");
					
?>
