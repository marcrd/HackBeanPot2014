<?php 
	$host = "us-cdbr-east-05.cleardb.net";
	$user = "b85ad415edfa4d";
	$pass = "df62fd56";
	
	$db = "hackbean";
	
	mysql_connect($host, $user, $pass);
	mysql_select_db($db);
	
	session_start();
	
	if(isset($_POST['search']))
	{
		require_once "Requests/Requests/library/Requests.php";
		Requests::register_autoloader();
		
		$search = $_POST['search'];
		
		//Get Request
		$request = Requests::get('http://api.tripadvisor.com/api/partner/1.0/search/'.$search.'?category=geos&key=92C34F58BB4F4E03894F5D171B79857E&limit=50');
		
		
		//Convert to array
		$obj = json_decode($request->body, true);
		
		//var_dump($obj);

		if(!isset($obj['geos'][0]))
		{
			header("Location: index.php?err=s");
			exit();
		}

		
		$longitude = $obj['geos'][0]['longitude'];
		$latitude = $obj['geos'][0]['latitude'];		
		
		if(isset($_SESSION['loggedin']))
		{
			$user = $_SESSION['userid'];
		}
		else
		{
			$user = -1;
		}
				
		$data;
	
		$data[0]['userid'] = $user;
		$data[0]['long'] = $_POST['long'];
		$data[0]['lat'] = $_POST['lat'];
		$data[1]['userid'] = -1;
		$data[1]['long'] = $longitude;
		$data[1]['lat'] = $latitude;
		
		$json = json_encode($data);
		if($result = mysql_query("INSERT INTO `heroku_807bde1acfd096e`.`group` (`users`, `date`) VALUES ('$json', NOW())"))
		{
			$id = mysql_insert_id();
			
			if($user != -1)
			{
				if($result2 = mysql_query("SELECT * FROM `heroku_807bde1acfd096e`.`hackbean` WHERE `id`=$user"))
				{
					if(mysql_num_rows($result2) > 0)
					{
						$row = mysql_fetch_assoc($result2);
				
						$data2 = json_decode($row['maps']);
						$data2[sizeof($data2)] = $id;
						$json2 = json_encode($data2);
							
						mysql_query("UPDATE `heroku_807bde1acfd096e`.`hackbean` SET maps='$json2' WHERE `ID`=$user" )
						or die(mysql_error());
				
						header("Location: map.php?id=".$id);
					}
				}
			}
			header("Location: map.php?id=".$id);
		}
		else
		{
			echo mysql_error();
		}
		/*$yourLat = $_POST['lat'];
		$yourLong = $_POST['long'];
		
		$midLong = ($longitude + $yourLong)/2;
		$midLat = ($latitude + $yourLat)/2;*/
		//$data;
		
		//$i = 0;
		//Go through each object and get data
		/*foreach($obj['data'] as $value)
		{
			$data[$i]['name'] = $value['name'];
			$data[$i]['latitude'] = $value['latitude'];
			$data[$i]['longitude'] = $value['longitude'];
			
			$i++;
		}*/
	}
	else
	{
		header("Location: index.php");
	}
?>

<!--  <form action="map.php" method="post" name="frm">
	<input type='hidden' name='midLong' value="<?php echo $midLong; ?>"/>
	<input type='hidden' name='midLat' value="<?php echo $midLat; ?>"/>
	<input type='hidden' name='long1' value="<?php echo $yourLong; ?>"/>
	<input type='hidden' name='lat1' value="<?php echo $yourLat; ?>"/>
	<input type='hidden' name='long2' value="<?php echo $longitude; ?>"/>
	<input type='hidden' name='lat2' value="<?php echo $latitude; ?>"/>
</form>

<script language="JavaScript">
	document.frm.submit();
</script>-->