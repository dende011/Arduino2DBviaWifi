<?php
  
  $light = $_GET['value'];


  //user information
  $host = "140.120.13.178";
  $user = "light";
  $pass = "light";

  //database information
  $databaseName = "lightdb";
  $tableName = "light";


  //Connect to mysql database
  $con = mysql_connect($host,$user,$pass);
  $dbs = mysql_select_db($databaseName, $con);


  //Query database for data
    $result = mysql_query("insert into $tableName (data) VALUES ($light)");

  //store matrix
  if($result==1)
    echo "success";
  else
    echo "error";
?>