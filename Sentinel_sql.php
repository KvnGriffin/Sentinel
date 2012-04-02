<?php
  /****** Note: this file is only ever run once as it creates the database and the tables*****/
  
   
 /***** Start database connection *****/
 $con = mysql_connect("localhost","root","BandWat250");
 if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

  /****** Create database, only created once *******/
if (mysql_query("CREATE DATABASE Sentinel_search",$con))
  {
  echo "Database created";
  }
else
  {
  echo "Error creating database: " . mysql_error();
  }

 /******   Create tables   ************/
 mysql_select_db("Sentinel_search", $con);
 $sql1 = "CREATE TABLE Yahoo
 (
 Rank int(10),
 Url varchar(100),
 Title varchar(100),
 Description varchar(500),
 DisplayUrl varchar(100),
 Score int(10)
 )";

 $sql2 = "CREATE TABLE Bing
 (
 Rank int(10),
 Url varchar(100),
 Title varchar(100),
 Description varchar(500),
 DisplayUrl varchar(100),
 Score int(10)
 )";

 $sql3 = "CREATE TABLE Blekko
 (
 Rank int(10),
 Url varchar(100),
 Title varchar(100),
 Description varchar(500),
 DisplayUrl varchar(100),
 Score int(10)
 )";


 $sql4 = "CREATE TABLE Google
 (
 Rank int(10),
 Url varchar(100),
 Title varchar(100),
 Description varchar(500),
 DisplayUrl varchar(100)
 )";

 $sql5 = "CREATE TABLE Bing_rewrite
 (
 Rank int(10),
 Url varchar(100),
 Title varchar(100),
 Description varchar(500),
 DisplayUrl varchar(100),
 Score int(10)
 )";

 $sql6 = "CREATE TABLE Blekko_rewrite
 (
 Rank int(10),
 Url varchar(100),
 Title varchar(100),
 Description varchar(500),
 DisplayUrl varchar(100),
 Score int(10)
 )";


 $sql7 = "CREATE TABLE Yahoo_rewrite
 (
 Rank int(10),
 Url varchar(100),
 Title varchar(100),
 Description varchar(500),
 DisplayUrl varchar(100),
 Score int(10)
 )";


/**** Execute queries ************/
 mysql_query($sql1, $con);
 mysql_query($sql2, $con);
 mysql_query($sql3, $con);
 mysql_query($sql4, $con);
 mysql_query($sql5, $con);
 mysql_query($sql6, $con);
 mysql_query($sql7, $con);
 
/***** Close connection **********/
 mysql_close($con);
 
?>
