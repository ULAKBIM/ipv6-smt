<?php 
if (!include('init.php')) 
{
    die("ERROR: Can not find init.php !");
}
?>

<html>
	<head>
		<title>IPv6 Services Monitoring Tool (IPv6-SMT)</title>
		
		<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
		<meta name="author" content="Emre Yüce - ULAKBİM" />
		<meta name="description" content="IPv6 Services (www,ns,mx) Monitoring Tool (IPv6-SMT)" />
		<meta name="keywords" content="ipv6,www,http,dns,mx" />
		<meta name="robots" content="index, follow, noarchive" />
		<meta name="googlebot" content="noarchive" />

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $config_arr->html->css; ?>" />
		<link rel="stylesheet" type="text/css" media="screen" href="style.css" />
		<script type="text/javascript" src="<?php echo $config_arr->html->js; ?>"></script>
		
		<script type="text/javascript" src="js/tabber.js"></script>
		<link rel="stylesheet" href="css/example.css" TYPE="text/css" MEDIA="screen">
		

		<script type="text/javascript">

			/* Optional: Temporarily hide the "tabber" class so it does not "flash"
			   on the page as plain HTML. After tabber runs, the class is changed
			   to "tabberlive" and it will appear. */

			document.write('<style type="text/css">.tabber{display:none;}<\/style>');
		</script>
	</head>
	
	<body>
	
		<div class="tabber">

			<div class="tabbertab">
				<h2>IPv6 Services Monitoring Details</h2>
			  
				<table class="ref sortable" >
					<thead>
					
					<tr>
						<th scope="col" title="Order by Name">Name</th>
						<th scope="col" title="Order by Domain">Domain</th>
						<th scope="col" title="Order by IPv6 Prefix status">IPv6 Prefix</th>
						<th scope="col" title="Order by WWW status">WWW</th>
						<th scope="col" title="Order by NS status">NS</th>
						<th scope="col" title="Order by MX status">MX</th>
					</tr>
	
					</thead>
					<tbody>
			
					<?php
						foreach ($domain_arr as $domain_arr_item) 
						{
					?>
					<tr>
						<td class="name"><b><?php echo $domain_arr_item->name; ?></b></td>
						<td class="name"><b><?php echo $domain_arr_item->domain; ?></b></td>
		
						<!--IPv6 address block allocation-->
						<?php
						switch (is_numeric($domain_arr_item->IPv6_allocated)) 
							{
								case 0:
									$color = 'red';
									$key = 3;
									break;
								case 1:
									$color = 'green';
									$key = 1;
									break;
								default:
									$color = 'yellow';
									$key = 2;
							}
						?>
						<td sorttable_customkey="<?php echo $key; ?>" class="<?php echo $color; ?>" style="text-align:center;">
							<b><?php echo $domain_arr_item->IPv6_allocated; ?></b>
						</td>
		
						<!--WWW-->
						<?php 
						if($domain_arr_item->WWW->IPv6_support && $domain_arr_item->WWW->IPv6_check)
						{
							$color='green';
							$key=1;
						}
						elseif ($domain_arr_item->WWW->IPv6_support)
						{
							$color='yellow';
							$key=2;
						}
						else
						{
							$color='red';
							$key=3;
						}
						 ?>
						<td sorttable_customkey="<?php echo $key; ?>" class="<?php echo $color; ?>">
						<?php
							if ($domain_arr_item->WWW->IPv6_check)
							{?>
								<a href="http://<?php echo $domain_arr_item->domain_www; ?>" target="_blank"><img src="<?php echo $config_arr->html->images; ?>/button-ipv6-small.png" style="width:50px;border:0;"/></a>
							<?php }?>

						</td>
		
						<!--NS-->
						<?php
							switch ($domain_arr_item->Count->NS->IPv6_check/$domain_arr_item->Count->NS->Total) 
							{
								case 0:
									$color = 'red';
									$key = 3;
									break;
								case 1:
									$color = 'green';
									$key = 1;
									break;
								default:
									$color = 'yellow';
									$key = 2;
							}
						?>
						<td sorttable_customkey="<?php echo $key; ?>" class="<?php echo $color; ?>">
							<?php echo $domain_arr_item->Count->NS->IPv6_check; ?>/<?php echo $domain_arr_item->Count->NS->IPv6_support; ?>/<?php echo $domain_arr_item->Count->NS->Total; ?>
						</td>
		
						<!--MX-->
						<?php
							switch ($domain_arr_item->Count->MX->IPv6_check/$domain_arr_item->Count->MX->Total) 
							{
								case 0:
									$color = 'red';
									$key = 3;
									break;
								case 1:
									$color = 'green';
									$key = 1;
									break;
								default:
									$color = 'yellow';
									$key = 2;
							}
						?>
						<td sorttable_customkey="<?php echo $key; ?>" class="<?php echo $color; ?>">
							<?php echo $domain_arr_item->Count->MX->IPv6_check; ?>/<?php echo $domain_arr_item->Count->MX->IPv6_support; ?>/<?php echo $domain_arr_item->Count->MX->Total; ?>
						</td>

					</tr>
					<?php	}?>
					</tbody>
	
					<tfoot>
			
					<tr>
						<td colspan="6" class="footer">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" rowspan="3" class="white"><b>Total: <?php echo $count_arr->Total; ?></b></td>
						<td class="red"><?php echo $count_arr->IPv6_prefix->r; ?></td>
						<td class="red"><?php echo $count_arr->WWW->r; ?></td>
						<td class="red"><?php echo $count_arr->NS->r; ?></td>
						<td class="red"><?php echo $count_arr->MX->r; ?></td>
					</tr>
					<tr>
						<td class="yellow"><?php echo $count_arr->IPv6_prefix->y; ?></td>
						<td class="yellow"><?php echo $count_arr->WWW->y; ?></td>
						<td class="yellow"><?php echo $count_arr->NS->y; ?></td>
						<td class="yellow"><?php echo $count_arr->MX->y; ?></td>
					</tr>
					<tr>
						<td class="green"><?php echo $count_arr->IPv6_prefix->g; ?></td>
						<td class="green"><?php echo $count_arr->WWW->g; ?></td>
						<td class="green"><?php echo $count_arr->NS->g; ?></td>
						<td class="green"><?php echo $count_arr->MX->g; ?></td>
					</tr>
					
					<tr>
						<td colspan="6"><b><i>Table is sortable by clicking the headers.</i></b></td>
					</tr>
			
					</tfoot>
				</table>
			  
			</div>


			<div class="tabbertab">
			  <h2>Archives</h2>
			  
				<table class="ref">
					<thead>
						<th colspan="5">You are viewing status for <?php echo $req_date; ?></th>
					</thead>
					<tbody>
					
					<tr>
						<td colspan="2">
							Select another date from archives:
						</td>
						<td colspan="3">
							 
								<select name="select1" onchange="redirect(this.value)"> 
									<option> &lt;-- Select Date --&gt; </option>
									<?php 
									foreach($files as $file)
									{
										//if filesize is too small skip the file
										if ((round(filesize($file), 2))<100)
											continue;
					
										$option = explode('/',$file);
										$opt = explode('.',$option[sizeof($option)-1]);
										$opt2 = explode('_',$opt[0]);
									?>
										<option value="?req_date=<?php echo $opt2[1]; ?>"><?php echo $opt2[1]; ?></option>
									<?php
									}
									?>
								</select> 
						</td>
					</tr>
					
					<tr>
						<td>&nbsp;</td>
						<td>IPv6 Prefix</td>
						<td>WWW</td>
						<td>NS</td>
						<td>MX</td>
					</tr>
					<tr>
						<td rowspan="3" class="white"><b>Total: <?php echo $count_arr->Total; ?></b></td>
						<td class="red"><?php echo $count_arr->IPv6_prefix->r; ?></td>
						<td class="red"><?php echo $count_arr->WWW->r; ?></td>
						<td class="red"><?php echo $count_arr->NS->r; ?></td>
						<td class="red"><?php echo $count_arr->MX->r; ?></td>
					</tr>
					<tr>
						<td class="yellow"><?php echo $count_arr->IPv6_prefix->y; ?></td>
						<td class="yellow"><?php echo $count_arr->WWW->y; ?></td>
						<td class="yellow"><?php echo $count_arr->NS->y; ?></td>
						<td class="yellow"><?php echo $count_arr->MX->y; ?></td>
					</tr>
					<tr>
						<td class="green"><?php echo $count_arr->IPv6_prefix->g; ?></td>
						<td class="green"><?php echo $count_arr->WWW->g; ?></td>
						<td class="green"><?php echo $count_arr->NS->g; ?></td>
						<td class="green"><?php echo $count_arr->MX->g; ?></td>
					</tr>
					</tbody>
				</table>
			  
			</div>

			<div class="tabbertab">
				<h2>Legend</h2>
			  
				<table class="ref">
					<thead>
					<tr>
						<th>Service</th>
						<th>Legend</th>
						<th>Description</th>
					</tr>
					</thead>
					<tbody>
						<!--Prefix-->
						<tr>
							<td class="name" rowspan="2">IPv6 Prefix</td>
							<td class="green">/48</td>
							<td align="left">Institution has allocated IPv6 address block of size /48.</td>
						</tr>
						<tr>
							<td class="red">-</td>
							<td align="left">Institution has NOT allocated IPv6 address block yet.</td>
						</tr>
		
						<!--WWW-->
						<tr>
							<td class="name" rowspan="3">WWW</td>
							<td class="green"><img src="<?php echo $config_arr->html->images; ?>/button-ipv6-small.png" style="width:50px;border:0;" /></td>
							<td align="left">Web server fully supports IPv6</td>
						</tr>
						<tr>
							<td class="yellow">&nbsp;</td>
							<td align="left">Web server has IPv6 address, but HTTP request over IPv6 is NOT successful.</td>
						</tr>
						<tr>
							<td class="red">&nbsp;</td>
							<td align="left">Web server has NO IPv6 address yet.</td>
						</tr>
		
						<!--NS MX-->
						<tr>
							<td class="name" rowspan="3">NS <br />MX</td>
							<td class="green">2/2/2</td>
							<td align="left">2 NS (MX) requests over IPv6 are successful / 2 NS (MX) has been assigned IPv6 addresses / There are 2 NS (MX)</td>
						</tr>
						<tr>
							<td class="yellow">1/2/3</td>
							<td align="left">1 NS (MX) requests over IPv6 are successful / 2 NS (MX) has been assigned IPv6 addresses / There are 3 NS (MX)</td>
						</tr>
						<tr>
							<td class="red">0/1/2</td>
							<td align="left">0 NS (MX) requests over IPv6 are successful / 1 NS (MX) has been assigned IPv6 addresses / There are 2 NS (MX)</td>
						</tr>
		
						
					</tbody>
				</table>
			  
			</div>
			
			<div class="tabbertab">
				<h2>Credits</h2>
				<table class="ref">
					<thead>
						<th colspan="2">IPv6 Services Monitoring Tool (IPv6-SMT) </th>
					</thead>
					
					<tbody>
						<tr>
							<td colspan="2">IPv6 Services Monitoring Tool (IPV6-SMT) is a free tool that monitors http, dns and smtp services and checks whether these services are available over IPv6. IPv6-SMT queries a given list of domain names and check for AAAA, NS and MX records.</td>
						</tr>
						<tr>
							<td>Project page:</td>
							<td><a href="https://github.com/ULAKBIM/ipv6-smt" title="IPv6 Services Monitoring Tool" target="_blank">https://github.com/ULAKBIM/ipv6-smt</a></td>
						</tr>
						<tr>
							<td>Version:</td>
							<td>0.2</td>
						</tr>
						<tr>
							<td>Release date:</td>
							<td>17.08.2012</td>
						</tr>
						<tr>
							<td>License:</td>
							<td>GPL 2.0</td>
						</tr>		
						<tr>
							<td>Related:</td>
							<td><a href="http://www.gen6.eu" title="GEN6" target="_blank">Governments ENabled with IPv6 (GEN6) Project</a></td>
						</tr>
					</tbody>
					
					<tfoot>
						<td colspan="2"><?php echo date("Y");?></td>
					</tfoot>
				</table>
				
			</div>

		</div>

	</body>
</html>
