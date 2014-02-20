<?php 

	session_start();
	$userid = $_GET['id'];
	
	$host = "us-cdbr-east-05.cleardb.net";
	$user = "b85ad415edfa4d";
	$pass = "df62fd56";
	
	$db = "hackbean";
	
	mysql_connect($host, $user, $pass);
	mysql_select_db($db);
	$data;
	
	if($result = mysql_query("SELECT * FROM `heroku_807bde1acfd096e`.`group` WHERE `ID`='$userid'"))
	{		
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_assoc($result);
			
			//Decode Group Data!
			$data = json_decode($row['users'], true);			
		}
		else
		{
			header("Location: index.php");	
		}
	}
	else
	{
		header("Location: index.php");
	}
	
	
	
	require_once "Requests/Requests/library/Requests.php";
	Requests::register_autoloader();
	
	$i = 0;
	$long = 0;
	$lat = 0;
	
	//Calculate average point
	foreach($data as $value)
	{
		$long += $value['long'];
		$lat += $value['lat'];
		$i++;
	}
	$long = $long / $i;
	$lat = $lat / $i;
	
	//Get Request
	$request = Requests::get('http://api.tripadvisor.com/api/partner/1.0/map/'.$lat.','.$long.'?distance=25&key=92C34F58BB4F4E03894F5D171B79857E&limit=50');
	
	//Convert to array
	$obj = json_decode($request->body, true);
	
	$data;
	
	$i = 0;
	//Go through each object and get data
	foreach($obj['data'] as $value)
	{
		$data[$i]['name'] = $value['name'];
		$data[$i]['latitude'] = $value['latitude'];
		$data[$i]['longitude'] = $value['longitude'];
		$data[$i]['desc'] = $value['description'];
		$data[$i]['street'] = $value['address_obj']['street1'];
		$data[$i]['citystate'] = $value['address_obj']['city'] . ", " . $value['address_obj']['state'];
		$data[$i]['postalcode'] = $value['address_obj']['postalcode'];  
		$data[$i]['weburl'] = $value['web_url'];
		$i++;
	}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>HotSpot!</title>
      
    <script>
		<?php 
			$js_array = json_encode($data);
			echo "var place = " . $js_array . ";\n";
		?>
    </script>
    <script src="jquery.js"></script>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
      
    <style>
      html, body, #map-canvas {
            
        height: 100%;
        margin-top: 10px;
        margin-left: 0px;
        maring-right: 0px;
        padding: 0px;
        overflow: hidden;
      }
        
    </style>
      
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
      
    <script>

    $(window).scroll(function() {
        scroll(0,0);
    });
    
	var map;
	        
	function initialize() {

	  var lati, longi;

	  if(typeof place[0]['latitude'] != 'undefined')
	  {
		  lati = place[0]['latitude'];
		  longi = place[0]['longitude'];
	  }
	  else
	  {
		  lati = <?php echo $data[0]['lat']; ?>;
		  longi = <?php echo $data[0]['long']; ?>;
	  }
	  var mapOptions = {
	    zoom: 10,
	    center: new google.maps.LatLng(lati,longi)
	  };
	    
	  map = new google.maps.Map(document.getElementById('map-canvas'),
	      mapOptions);

	  //Info window variable
	  var infowindow =  new google.maps.InfoWindow({
           content: ""
      });

      //Loop through all the markers
	  for(var i = 0; i < place.length; i++)
	  {
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(place[i]['latitude'], place[i]['longitude']),
			map: map,
			title: place[i]['name']	
		});

		$content = "<div style='font-weight:400; font-size: 14px'>" + place[i]['name'] + "</div>" +
		"<div style='font-size: 14px'>" + place[i]['street'] + ", " + place[i]['citystate'] + "</div><br/>" +
		 "<a href='" + place[i]['weburl'] + "'>"+place[i]['name']+"'s TripAdvisor Review</a><br/><i>"+	 place[i]['desc']+"</i><br/>" + 
	
		"<img src='http://maps.googleapis.com/maps/api/streetview?size=250x100&location="+place[i]['latitude']+","+place[i]['longitude']+"&fov=90&heading=235&pitch=10&sensor=false'/>" 
			;
		//Bind Info window and marker
		bindInfoWindow(marker, map, infowindow, $content);
	  }


	  drawYou();

		// Try W3C Geolocation (Preferred)
				  if(navigator.geolocation) {
				    browserSupportFlag = true;
				    navigator.geolocation.getCurrentPosition(function(position) 
						{
				      	initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
				    	 initialLat = position.coords.latitude;
						 initialLong = position.coords.longitude;
						 document.getElementById("latid").value = initialLat;
						 document.getElementById("longid").value = initialLong;
						
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

	//Closer loop fix
	function bindInfoWindow(marker, map, infowindow, strDescription) {
	    google.maps.event.addListener(marker, 'click', function() {
	        infowindow.setContent(strDescription);
	        infowindow.open(map, marker);
	    });
	}



	function drawPerson(latitude, longitude)
	{
		 var pinColor = "7777FF";
		 var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
			       new google.maps.Size(21, 34),
			       new google.maps.Point(0,0),
			       new google.maps.Point(10, 34));
	        
		 new google.maps.Marker({
			  position: new google.maps.LatLng(latitude,  longitude),
			  map: map,
			  icon: pinImage,
			  title: "Person 1"
		 });
		 new google.maps.Circle({
				strokeColor: '#7777FF',
				strokeOpacity: 0.6,
				strokeWeight: 2,
				fillColor: '#7777FF',
				fillOpacity: 0.15,
				map:map,
				center: new google.maps.LatLng(latitude, longitude),
			radius: 500
		 });			  
	}

	
		function drawYou()
		{
		  <?php 
		  //Display each person
		  foreach($data as $value)
		  {
		  	if(isset($value['lat']))
		  		echo "drawPerson(" . $value['lat'] . ", " . $value['long'] . ");";		 
		  }
		  ?>
		}

    </script>
    <style>
    	.textboxs
    	{
    		width: 180px; 
    		margin: 5px; 
    		height: 30px; 
    		border-radius: 10px; 
    		background-color: #F0F0F0; 
    		border:1px solid #DDD;
    		outline-width: 0;
    		padding-left: 10px;
    	}
    	.textboxs:focus
    	{
    		background-color:#FFF;
    	}
    	
    	.logo
    	{
    		float: left; 
    		width: 200px; 
    		font-family: Arial; 
    		font-size: 30px; 
    		margin-top:2px;
    	}
    	.logo:hover
    	{
    		color: #FFF;
    	}
    	    	
    	.navbar
    	{
    		width: 100%; 
    		height: 45px; 
    		background-color: #777;
    		box-shadow: 0px 0px 3px #000;
    		position: absolute; 
    		top: 0px;
    		left: 0px;
    		z-index: 10;
    		overflow: hidden;
    	}
    	
    	.submitButton
    	{
    		background-color: #F0F0F0;
    		border: 1px solid #DDD;
    		height: 30px;
    		width: 70px;
    		border-radius: 5px;
    	}
    	.submitButton:hover
    	{	
    		cursor: pointer;
    		background-color: #FFF;
    	}
    
    	.selectionBar
    	{
    		width: 200px;
    		height: 100%;
    		position: absolute; 
    		right: 0px;
    		margin-top: 15px;
    		background-color: #666;
    		box-shadow: 0px 0px 3px #000;	
    		z-index: 8;
    		overflow-y: scroll;
    		overflow-x: visable;
    	}
    	.selectionObject
    	{
    		width: 150px; height: 130px;
    		border: 2px solid #777;
    		padding: 5px;
    		color: #CCC;
    		font-family: Arial;
    		margin: 10px;
    		margin-left: 10px;
    		box-shadow: 0px 0px 2px #000;
    	}
    	.selectionObject:hover
    	{
    		background-color: #777777;
    		cursor: pointer;
    	}
    	.selectionRater
    	{
    		width: 40px; 
    		right: 190px; 
    		height: 120px;
    		top: 25px;
    		position: absolute;
    		z-index: 10;
    		text-align: center;
    	}
    	.dropdownArrow
    	{
    		width: 200px; 
    		padding-top: 10px; 
    		text-align: center; 
    		float: right;
    		color: #CCC;
    		font-family:Arial;
    		height: 40px;
    	}
    	.dropdownArrow:hover
    	{
    		background-color: #666666;
    		cursor: pointer;
    	}
    	::-webkit-scrollbar {
		    height: 12px;
			width: 12px;
			background: #000;
		    }
		::-webkit-scrollbar-thumb {
		    background: #707070;
		    -webkit-border-radius: 1ex;
		    -webkit-box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.75);
		}
		::-webkit-scrollbar-corner {
		    background: #000;
		}
		
		.backbutton:hover
		{
			background-color: #888;
			cursor: pointer;
		}
    </style>
  </head>
  <body>
	<div class="navbar">
		<div id="backbut" class="backbutton" style="float: left; font-family: Arial; padding-left: 5px; padding-top: 13px; width: 60px; color: #DDD; height: 45px;">
			&#9668; Back 
		</div>
		<script>
			$('#backbut').click( function(){ window.location = "index.php"; });
		</script>
		<form action="group.php" method="post" style="display:inline; margin: 0px; padding: 0px;">
			<input class="textboxs" placeholder="Add New Location" type="textbox" name="search"/>
			<input class="submitButton" type="submit" />
			<input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
			<input type="hidden" name="addToGroupSearch"/>
		</form>
		
		<?php 
		$flag = true;
		foreach($data as $value)
		{
			if(isset($value['userid']))
			{
				if($value['userid'] == $_SESSION['userid'])
				{
					$flag = false;
				}
			}
		}
		if($flag)
		{
		?>
		<form action="group.php" method="post" style="display:inline;margin: 0px; padding: 0px;">
			<input class="submitButton" type="submit" style="margin-left: 90px; width: 200px;" value="Add Your Location"/>
			<input type="hidden" name="lat" id="latid"/>
			<input type="hidden" name="long" id="longid"/>
			<input type="hidden" name="addToGroupGeo"/>
			<input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
		</form>
		<?php }?>
		<div id="dropdownArrow" class="dropdownArrow">
			Places &#x25BC;
		</div>
	</div>
	<div class="selectionBar" id="selectionBar">
		<script>
		 for(var i = 0; i < place.length; i++)
		 {
			 var content = 
				 "<div id='place"+i+"' class='selectionObject'>" +
				 	"<div class='selectionRater'>" +
				 	"</div>" +
				 	"<div style='font-weight:400; font-size: 14px'>" + 
				 		place[i]['name'] + 
					 "</div>"+
					 "<img style='margin:5px' src='http://maps.googleapis.com/maps/api/streetview?size=130x80&location="+place[i]['latitude']+","+place[i]['longitude']+"&fov=90&heading=235&pitch=10&sensor=false'/>" +
				"<script>< /script>"+
				"</div>";
			 document.getElementById('selectionBar').innerHTML = document.getElementById('selectionBar').innerHTML + content;
		 }

		</script>
	</div>
	<script>
	$('#dropdownArrow').click(function()
	{
		$('#selectionBar').slideToggle();
	});
	</script>
    <div id="map-canvas"></div>
       
   <!-- <img src="hotspot.png" style="opacity: 1; margin: 5px; position: absolute; bottom: 10px; left: 10px;" height="200px" width="200px"/>-->
  </body>
</html>