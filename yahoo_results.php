<?php
   /*********** Start database connection  ********/ 
     $con = mysql_connect("localhost","root","BandWat250");
     if (!$con)
     {  
       die('Could not connect: ' . mysql_error());
     }

     mysql_select_db("Sentinel_search", $con);
      
     /********* Select all info from table Yahoo ********/ 
     $query="SELECT * FROM Yahoo";
     $result=mysql_query($query);
       
     /********* Echo results in format below ****************/
     echo " Search results from Yahoo";
     while($row = mysql_fetch_assoc( $result))
        {
	       echo "<p>".$row['Rank'].": <a href='".$row['Url']."'>".$row['Title']."</a><br /><i>".$row['Description']."</i><br /><span style='font-size: 10pt;'>".$row['DisplayUrl']."</span></p>"; // display everything
        }
      
      
?>
