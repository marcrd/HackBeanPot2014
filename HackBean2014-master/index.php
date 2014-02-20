<?php 
	
	session_start();

	$host = "us-cdbr-east-05.cleardb.net";
	$user = "b85ad415edfa4d";
	$pass = "df62fd56";
	
	$db = "hackbean";
	
	mysql_connect($host, $user, $pass);
	mysql_select_db($db);
	
	/* LOGIN */
	if(isset($_POST['login']))
	{
		//Terribly insecure way to log in, to be changed
		$user = mysql_real_escape_string($_POST['username']);
		$password = mysql_real_escape_string($_POST['password']);
		
		if($result = mysql_query("SELECT * FROM `heroku_807bde1acfd096e`.`hackbean` WHERE `username` = '$user'"))
		{		
			$row = mysql_fetch_assoc($result);
			
			//If password matches then go for it
			if(strcmp($row['password'], crypt($password, $row['password'])) == 0)
			{
				$_SESSION['userid'] = $row['ID'];
				$_SESSION['username'] = $row['username'];
				$_SESSION['loggedin'] = true;
			}
			else
			{
				echo "no";
			}
		}	
		else
		{
			echo mysql_error();
		}
	}
	/* CREATE ACCOUNT */
	if(isset($_POST['CreateAccount']))
	{
		//Terribly insecure way to log in, to be changed
		$user = mysql_real_escape_string($_POST['username']);
		$password = mysql_real_escape_string($_POST['password']);
		
		//Try to find usernames that already exist
		if($result = mysql_query("SELECT username FROM `heroku_807bde1acfd096e`.`hackbean` WHERE `username` = '$user'"))
		{
			//If Username is unique
			if(mysql_num_rows($result) == 0)
			{
				//If Password is valid (not null and hasn't changed through the escape process
				if(strcmp($password,"" ) != 0 && strcmp($password, $_POST['password'])==0)
				{	
					//Crypt Password and send 
					$password = crypt($password);
					mysql_query("INSERT INTO `heroku_807bde1acfd096e`.`hackbean` (`username`, `password`) VALUES ('$user', '$password')")
						or die(mysql_error());
						
					$_SESSION['userid'] = mysql_insert_id();
					$_SESSION['username'] = $user;
					$_SESSION['loggedin'] = true;
				}
				else
				{
					echo "Invalid Password";
				}
			}
			else
			{
				echo "Username already being used";
			}
		}
	}
	if(isset($_POST['SignOut']))
	{
		session_destroy();
		header("Location: index.php");
	}
	
	$yourMaps;
	if(isset($_SESSION['loggedin']))
	{
		$userid = $_SESSION['userid'];
		if($result = mysql_query("SELECT * FROM `heroku_807bde1acfd096e`.`hackbean` WHERE `ID` = $userid"))
		{
			if(mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_assoc($result);
				
				$yourMaps = json_decode($row['maps']);
			}
			
		}
	}
?>

<html>
	<head>
        <title>CrossPath - Home</title>
        <link rel="stylesheet" href="styles.css" />
        <script src="modernizr.js"></script>
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<script src="jquery.js"></script>
   		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
		<script>
			var initialLong = 0;
			var initialLat = 0;

			function initialize() {

			  // Try W3C Geolocation (Preferred)
			  if(navigator.geolocation) {
			    browserSupportFlag = true;
			    navigator.geolocation.getCurrentPosition(function(position) 
					{
			      	initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			    	 initialLat = position.coords.latitude;
					 initialLong = position.coords.longitude;
					 document.getElementById("yourLat2").value = initialLat;
					 document.getElementById("yourLong2").value = initialLong;
					 document.getElementById("yourSearch").value = "Your location has been added";
					 document.getElementById("yourLat").value = initialLat;
					 document.getElementById("yourLong").value = initialLong;

					
			    }, function() {
			      handleNoGeolocation(browserSupportFlag);
			    });
			  }
			  // Browser doesn't support Geolocation
			  else {
			    browserSupportFlag = false;
			    handleNoGeolocation(browserSupportFlag);
			  }
			
			  function handleNoGeolocation(errorFlag) {
			    if (errorFlag == true) {
			      alert("Geolocation service failed.");
			      initialLocation = newyork;
			    } else {
			      alert("Your browser doesn't support geolocation. We've placed you in Siberia.");
			      initialLocation = siberia;
			    }
			    
			  }
			}
			google.maps.event.addDomListener(window, 'load', initialize);
		</script>
	</head>
	
    
    
	<body style="margin: 0px; padding:0px">
        
        <div id="topbar" width="100%"; > 
        		<form action="index.php" method="post">
            
            <!-- Login code --> 
           <?php if(!isset($_SESSION['loggedin']))
           {
           
           ?>
            <div style="height: 45px; width: 200px; position: absolute; float: rigt; right: 215px;">
				<input type="text" class="username" placeholder="Username" name="username"/><br>
            </div>
            <div style="height: 45px; width: 200px; position: absolute; float: right; right: 30px;">
            	<input type="password" class="password" placeholder="Password" name="password"/>
			</div>
			<input type="hidden" name="login"/>
            <input type="submit" class="login" name="login" > 
            <?php }
			else {
				
			}?>
		</form></div>
        
        <div id="login"; ></div>
        
       <!-- <div id="breadcrumb";></div> -->
        
        <div id="box";></div>
        
        
        
        
       <img src="crosspath.png" href="http://secure-waters-3897.herokuapp.com/index.php"  class="logo" alt="HotSpot">  
   <!--    <img src="logintriangle.png" class="triangle">
        <img src="locarrowone.png" class="locarrowone">
        <img src="locarrowtwo.png" class="locarrowtwo">-->

        
      <!--  <div id="Hotspot";> 
        <p> HotSpot: Bringing People Together </p>
        </div> -->
        

		
            
            
            
		<?php if(!isset($_SESSION['loggedin']))
		{?>

		
	
		<?php }
		else 
		{ ?>
	
		<br/>
		<div class="user"> Hello, <?php echo $_SESSION['username'];?> 
		<form action="index.php" style="display:inline;" method="post">
			<input type="submit" class="logout" value="(logout)"/>
			<input type="hidden" name="SignOut"/>
		</form>
		</div>
		<br/><br/>
		<form action="group.php" method="post">
            <input type="submit" class="newgroup" value="New Group"/>
			<input id="yourLat2" type="hidden" name="lat" value=""/>
			<input id="yourLong2" type="hidden" name="long" value=""/>
			<input type="hidden" name="newGroup"/>
		</form>
		<?php 
		} ?>
		<br/><br/>
		
		<br/>
		<br/>
		<?php if(!isset($_SESSION['loggedin']))
		{?>
		<div style="width: 600px; height: 325px; text-align: center; background-color: #FFF; border-radius: 50px; margin: 0px auto; margin-top: 100px; box-shadow: 0px 0px 10px #000;">
			<div id="hotspot_main">
				<form action="find.php" method="post">
				 	
					<div style="width: 600px; height: 75px; font-size: 48px; font-family: Arial; color: #BBB;">CrossPath</div>
					<?php if(isset($_GET['needLogin'])) { ?> <div style="width: 600px; height: 25px; font-size: 16px; color: red; font-family: Arial;">To Add Location, Please Log In!</div><?php } ?>
					<?php if(isset($_GET['err'])) echo "<div style='color: red'>Error in search</div>"?><br/>
					
					<input class="inputBox3" readonly id="yourSearch" type="text"  placeholder="Goelocation not found yet..." name="search2"/>
					<br/>
					<input class="inputBox3" type="text" name="search"  placeholder="Where To?"/>
					<br/><br/>
					<input type="submit" class="submitbutton" value="Search"/>
					<input id="yourLat" type="hidden" name="lat"/>
					<input id="yourLong" type="hidden" name="long"/>
					<input id="yourLat2" type="hidden" name="lat"/>
					<input id="yourLong2" type="hidden" name="long"/>
					<div id="infologo" class="hoverdiv" style="font-size: 14px; margin-top: 10px; width: 500px; text-align: right; color: #0645AD;"><u>Create Account</u></div>
				</form>
			</div>
			<div id="create_main" style="display: none;">
				<form action="index.php" method="post">
					<br/>
					<div style="width: 600px;  height: 75px; font-size: 48px; font-family: Arial; color: #BBB;">Create New Account</div>
					<input type="text" class="inputBox3" name="username" placeholder="username"/><br>
					<input type="password" class="inputBox3" name="password" placeholder="password"/>
					<input type="hidden" name="CreateAccount"/><br/><br/>
					<input type="submit" class="submitbutton" value="Join CrossPath!"/> 
					<div id="backLogo" class="hoverdiv" style="font-size: 14px; width: 500px; text-align: right; color: #0645AD;"><u>Back</u></div>	
				</form> 
			<br/>
			</div>
			     <script>
     	$('#create_main').hide();
		$('#infologo').click(function(){ $('#hotspot_main').slideToggle(); $('#create_main').slideToggle(); });
		$('#backLogo').click(function(){ $('#hotspot_main').slideToggle(); $('#create_main').slideToggle(); });
	     </script>
		</div>
		<?php }
		else {//$yourMaps ?>
			<div style="width: 600px; height: 325px; text-align: center; background-color: #FFF; border-radius: 50px; margin: 0px auto; margin-top: 100px; box-shadow: 0px 0px 10px #000;">
				<div style="width: 600px;  height: 75px; font-size: 48px; font-family: Arial; color: #BBB;">Your Profile</div>
				<div style="float:left; overflow-y: scroll; overflow-x: hidden; margin-left: 50px; width: 200px; height: 250px; font-size: 18px; font-family: Arial;"> 
					<?php 
					if (is_array($yourMaps))
					{
						foreach($yourMaps as $tmap)
						{
							echo '<div id="cmap'.$tmap.'" class="maplistobject">Map #'.$tmap.'</div>';
							echo "<script>$('#cmap".$tmap."').click( function(){ window.location = \"map.php?id=".$tmap."\"});</script>";
						}
					}
					else
					{
						echo "You have no arrangments";
					}
					?>
				</div>
				<div style="width:350px; height: 200px; float:right;">
				<form action="find.php" method="post">
					<input class="inputBox3" readonly id="yourSearch" type="text"  placeholder="My Location..." style="width: 300px" name="search2"/>
					<br/>
					<input class="inputBox3" type="text" name="search" style="width:300px" placeholder="Where To?"/>
					<br/><br/>
					<input type="submit" class="submitbutton" value="Search"/>
					<input id="yourLat" type="hidden" name="lat"/>
					<input id="yourLong" type="hidden" name="long"/>
				</form>
				</div>
			
			</div>		
		<?php } ?>
		<br/><br/>

	</body>
</html>
