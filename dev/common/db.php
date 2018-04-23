<?php
	$dbInfo = array("DataBase"=>"dfever","UID"=>"sa","PWD"=>"dfever321!@#");

	// DB connect
	$dbConn = sqlsrv_connect("localhost",$dbInfo);
	//sqlsrv_query('set names utf8');

	if( !$dbConn ) {
		echo "Connection could not be established.<br />";
		die( print_r( sqlsrv_errors(), true));
	}
?>
