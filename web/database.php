<?php

  //user information
  $host = "server IP";
  $user = "使用者名稱";
  $pass = "使用者密碼";

  //database information
  $databaseName = "資料庫名稱";
  $tableName = "資料表名稱";


  //Connect to mysql database
  $con = mysql_connect($host,$user,$pass);
  $dbs = mysql_select_db($databaseName, $con);


  //Query database for data
  $result = mysql_query("SELECT * FROM $tableName");

  //store matrix
  $i=0;
  while ($row = mysql_fetch_array($result)){
    $employee[$i]=$row;
    $i++;
  }

  //echo result as json 
    echo json_encode($employee);
?>
