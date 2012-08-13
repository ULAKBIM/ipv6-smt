<?php include("init.php")?>
<!--
/////////////////////////////////////////////
// IPv6 Services Monitoring Tool (IPV6-SMT)
//
// Author: Emre Yüce - ULAKBİM
// Contact: emre.yuce[@]tubitak.gov.tr
// Version: 0.1
// Release date: 15.08.2012
// License: GPL 2.0 or later
// Current file: ipv6_smt.php
// 
// Thanks to Murat Soysal - ULAKBİM, Onur Bektaş - ULAKBİM, Uğur Yılmaz - ULAKBİM
// 
/////////////////////////////////////////////
-->

<html>
	<head>
		<title>IPv6 Services Monitoring Tool (IPv6-SMT)</title>
		
		<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
		<meta name="author" content="Emre Yüce - ULAKBİM" />
		<meta name="description" content="IPv6 Services (www,ns,mx) Monitoring Tool (IPv6-SMT)" />
		<meta name="keywords" content="ipv6,www,http,dns,mx" />
		<meta name="robots" content="index, follow, noarchive" />
		<meta name="googlebot" content="noarchive" />

		<link rel="stylesheet" type="text/css" media="screen" href="<?=$config_arr->html->css?>" />
		<script type="text/javascript" src="<?=$config_arr->html->js?>"></script>
	</head>
	
	<body>
	
		<h3>IPv6 Services Monitoring Tool (IPv6-SMT)</h3>
		<p>This page includes IPv6 service status of nodes connected to ULAKNET (Turkish Academic Network) which is run by ULAKBİM (Turkish NREN).</p>

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
					<td class="green"><img src="<?=$config_arr->html->images?>/button-ipv6-small.png" style="width:50px;border:0;" /></td>
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
		
				<tr>
					<td colspan="3"><b><i>Table is sortable by clicking the headers.</i></b></td>
				</tr>
			</tbody>
		</table>
		<br />

		<table class="ref">
			<thead>
			<tr>
				<th>Name</th>
				<th>Domain</th>
				<th>IPv6 Prefix</th>
				<th>WWW</th>
				<th>NS</th>
				<th>MX</th>
			</tr>
			<tr>
				<td colspan="2" rowspan="3" class="white"><b>Total: <?=$count_arr->Total?></b></td>
				<td class="red"><?=$count_arr->IPv6_prefix->r?></td>
				<td class="red"><?=$count_arr->WWW->r?></td>
				<td class="red"><?=$count_arr->NS->r?></td>
				<td class="red"><?=$count_arr->MX->r?></td>
			</tr>
			<tr>
				<td class="yellow"><?=$count_arr->IPv6_prefix->y?></td>
				<td class="yellow"><?=$count_arr->WWW->y?></td>
				<td class="yellow"><?=$count_arr->NS->y?></td>
				<td class="yellow"><?=$count_arr->MX->y?></td>
			</tr>
			<tr>
				<td class="green"><?=$count_arr->IPv6_prefix->g?></td>
				<td class="green"><?=$count_arr->WWW->g?></td>
				<td class="green"><?=$count_arr->NS->g?></td>
				<td class="green"><?=$count_arr->MX->g?></td>
			</tr>
			</thead>
			<tbody>
			<tr>
				<th colspan="3">
					Status for <?=$req_date?>
				</th>
				<th colspan="3">
					 
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
								<option value="?req_date=<?=$opt2[1]?>"><?=$opt2[1]?></option>
							<?
							}
							?>
						</select> 
				</th>
			</tr>
			</tbody>
		</table>
		<br />
		<table class="ref sortable">
			<thead>
	
			<tr>
				<th>Name</th>
				<th>Domain</th>
				<th>IPv6 Prefix</th>
				<th>WWW</th>
				<th>NS</th>
				<th>MX</th>
			</tr>
	
			</thead>
			<tbody>
			
			<?php
				foreach ($domain_arr as $domain_arr_item) 
				{
			?>
			<tr>
				<td class="name"><b><?=$domain_arr_item->name?></b></td>
				<td class="name"><b><?=$domain_arr_item->domain?></b></td>
		
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
				<td sorttable_customkey="<?=$key?>" class="<?=$color?>" style="text-align:center;">
					<b><?=$domain_arr_item->IPv6_allocated?></b>
				</td>
		
				<!--WWW-->
				<? 
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
				<td sorttable_customkey="<?=$key?>" class="<?=$color?>">
				<?php
					if ($domain_arr_item->WWW->IPv6_check)
					{?>
						<a href="http://www.<?=$domain_arr_item->domain?>" target="_blank"><img src="<?=$config_arr->html->images?>/button-ipv6-small.png" style="width:50px;border:0;"/></a>
					<?}?>

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
				<td sorttable_customkey="<?=$key?>" class="<?=$color?>">
					<?=$domain_arr_item->Count->NS->IPv6_check?>/<?=$domain_arr_item->Count->NS->IPv6_support?>/<?=$domain_arr_item->Count->NS->Total?>
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
				<td sorttable_customkey="<?=$key?>" class="<?=$color?>">
					<?=$domain_arr_item->Count->MX->IPv6_check?>/<?=$domain_arr_item->Count->MX->IPv6_support?>/<?=$domain_arr_item->Count->MX->Total?>
				</td>

			</tr>
			<?php	}?>
			</tbody>
	
			<tfoot>
			<tr>
				<th>Name</th>
				<th>Domain</th>
				<th>IPv6 Prefix</th>
				<th>WWW</th>
				<th>NS</th>
				<th>MX</th>
			</tr>
			<tr>
				<td colspan="6" class="footer">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2" rowspan="3" class="white"><b>Total: <?=$count_arr->Total?></b></td>
				<td class="red"><?=$count_arr->IPv6_prefix->r?></td>
				<td class="red"><?=$count_arr->WWW->r?></td>
				<td class="red"><?=$count_arr->NS->r?></td>
				<td class="red"><?=$count_arr->MX->r?></td>
			</tr>
			<tr>
				<td class="yellow"><?=$count_arr->IPv6_prefix->y?></td>
				<td class="yellow"><?=$count_arr->WWW->y?></td>
				<td class="yellow"><?=$count_arr->NS->y?></td>
				<td class="yellow"><?=$count_arr->MX->y?></td>
			</tr>
			<tr>
				<td class="green"><?=$count_arr->IPv6_prefix->g?></td>
				<td class="green"><?=$count_arr->WWW->g?></td>
				<td class="green"><?=$count_arr->NS->g?></td>
				<td class="green"><?=$count_arr->MX->g?></td>
			</tr>
			<tr>
				<td colspan="6" class="footer">ULAKBİM 2012</td>
			</tr>
			</tfoot>
		</table>
	</body>
</html>
