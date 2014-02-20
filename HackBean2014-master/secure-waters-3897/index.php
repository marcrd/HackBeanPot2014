<?php 
	
    echo "another test";
    
	//$host = "localhost";	
	//$user = "root";	
	//$pass = "";
	

	$host = "us-cdbr-east-05.cleardb.net";
	$user = "b85ad415edfa4d";
	$pass = "df62fd56";
		
	$db = "hackbean";
		
	mysql_connect($host, $user, $pass);
	mysql_select_db($db);
	
	mysql_query("INSERT INTO `heroku_807bde1acfd096e`.`hackbean` (`username`, `password`) VALUES ('Justin', 'test2')");
?>

