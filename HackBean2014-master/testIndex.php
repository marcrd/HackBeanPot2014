<html>
	<head>
		<title>HotSpot!</title>
        <link rel="stylesheet" href="styles.css" />
        <script src="modernizr.js"></script>
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	</head>
	
	<body>
        <div id="topbar" width="100%"; > </div>
        
        <div id="login"; ></div>
        
       <!-- <div id="breadcrumb";></div> -->
        
        <div id="box";></div>
        
        
        
        
        <img src="hotspot.png" href="http://secure-waters-3897.herokuapp.com/index.php"      class="logo" alt="HotSpot"> 
        <img src="logintriangle.png" class="triangle">
        <img src="locarrowone.png" class="locarrowone">
        <img src="locarrowtwo.png" class="locarrowtwo"> 
       <p><img src="infologo.png" class="infologo" href="registration.php"><span></span></p>

        
      <!--  <div id="Hotspot";> 
        <p> HotSpot: Bringing People Together </p>
        </div> -->
        
		<form action="index.php" method="post">
			
			<input type="text" class="username" placeholder="Username" name="username"/><br>
            <input type="password" class="password" placeholder="Password" name="password"/>
			<input type="hidden" name="login"/>
			
		</form>
		<br/>
		<br/>
		<!-- <form action="index.php" method="post">
            
            <input type="text" class="username" name="username" /><br>
			<input type="password" class="password" name="password"/>
			<input type="hidden" name="CreateAccount"/> 

		</form> -->

		
		<form action="find.php" method="post">
        
            
		
			<input type="text" class="inputone" placeholder="My Location..." name="search"/>
			<input type="text" class="inputtwo" placeholder="Their Location..." name="searchbox"/>
            
            <input type="submit" class="submitbutton" value="Search">
			
            
      
		</form>
	</body>
</html>
