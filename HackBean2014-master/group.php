<?php 
session_start();
if(isset($_SESSION['loggedin']))
{
	$host = "us-cdbr-east-05.cleardb.net";
	$user = "b85ad415edfa4d";
	$pass = "df62fd56";
	
	$db = "hackbean";
	
	mysql_connect($host, $user, $pass);
	mysql_select_db($db);
	
	if(isset($_POST['newGroup']))
	{		
		$user = $_SESSION['userid'];
		$long = $_POST['long'];
		$lat = $_POST['lat'];
		
		$data;
		$data[0]['userid'] = $user;
		$data[0]['long'] = $long;
		$data[0]['lat'] = $lat;
		
		$json = json_encode($data);
		if($result = mysql_query("INSERT INTO `heroku_807bde1acfd096e`.`group` (`users`, `date`) VALUES ('$json', NOW())"))
		{
			$id = mysql_insert_id();
			
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
		else
		{
			echo mysql_error();
		}
	}
	//Somebody wants to add a point by search
	if(isset($_POST['addToGroupSearch']))
	{
		//Load Request API
		require_once "Requests/Requests/library/Requests.php";
		Requests::register_autoloader();
		
		$search = $_POST['search'];
		
		if($search != "")
		{
			//Get Request
			$request = Requests::get('http://api.tripadvisor.com/api/partner/1.0/search/'.$search.'?category=geos&key=92C34F58BB4F4E03894F5D171B79857E&limit=50');
			
			//Convert to array
			$obj = json_decode($request->body, true);
			
		
			
			if(isset($obj['geos'][0]))
			{
				$lat = $obj['geos'][0]['latitude'];
				$long =$obj['geos'][0]['longitude'];
				
				$id = $_POST['id'];
				if($result = mysql_query("SELECT * FROM `heroku_807bde1acfd096e`.`group` WHERE id=$id"))
				{
					$row = mysql_fetch_assoc($result);
					
					$data = json_decode($row['users'], true);
					$i = sizeof($data);
					$data[$i]['userid'] = '-1';			
					$data[$i]['long'] = $long;
					$data[$i]['lat'] = $lat;
		
					$json = json_encode($data);
					echo $json;
					mysql_query("UPDATE `heroku_807bde1acfd096e`.`group` SET users='$json' WHERE id=$id ")
						or die(mysql_error());
						
					header("Location: map.php?id=".$id);					
				}	
				
			}
			else
			{
				$id = $_POST['id'];
				header("Location: map.php?id=".$id);
			}
		}	
	}
	else if(isset($_POST['addToGroupGeo']))
	{
		$lat = $_POST['lat'];
		$long = $_POST['long'];
		$id = $_POST['id'];	
		
		echo $lat;
		echo $long;
		if($result = mysql_query("SELECT * FROM `heroku_807bde1acfd096e`.`group` WHERE id=$id"))
		{
			$row = mysql_fetch_assoc($result);
				
			$data = json_decode($row['users'], true);
			$i = sizeof($data);
			$data[$i]['userid'] = $_SESSION['userid'];
			$data[$i]['long'] = $long;
			$data[$i]['lat'] = $lat;
		
			$json = json_encode($data);
			echo $json;
			mysql_query("UPDATE `heroku_807bde1acfd096e`.`group` SET users='$json' WHERE id=$id ")
			or die(mysql_error());
		
			$user = $_SESSION['userid'];
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
	}
}
else
{
	
	header("Location: index.php?needLogin=true");	
}

?>