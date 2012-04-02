<?php
   /*********** Start database connection  ********/ 
     $con = mysql_connect("localhost","root","BandWat250");
     if (!$con)
     {  
       die('Could not connect: ' . mysql_error());
     }

     mysql_select_db("Sentinel_search", $con);
     
     /******* Select all info from each table ***********/
     $query_yahoo="SELECT * FROM Yahoo";
     $result_yahoo=mysql_query($query_yahoo);
     
     $query_bing="SELECT * FROM Bing";
     $result_bing=mysql_query($query_bing);
     
     $query_blekko = "SELECT * FROM Blekko";
     $result_blekko = mysql_query($query_blekko);
       
       
    /******* Echo all to screen in format below *********/   
       echo "<br>";
       echo "<b>Results from Yahoo</b>";
       while($row = mysql_fetch_assoc( $result_yahoo))
        {
	       echo "<p>".$row['Rank'].": <a href='".$row['Url']."'>".$row['Title']."</a><br /><i>".$row['Description']."</i><br /><span style='font-size: 10pt;'>".$row['DisplayUrl']."</span></p>"; // display everything
        }
        
       echo "<br>";
       echo "<b>Results from Bing</b>";
       while($row2 = mysql_fetch_assoc( $result_bing))
        {
	       echo "<p>".$row2['Rank'].": <a href='".$row2['Url']."'>".$row2['Title']."</a><br /><i>".$row2['Description']."</i><br /><span style='font-size: 10pt;'>".$row2['DisplayUrl']."</span></p>"; // display everything
        }
       
       echo "<br>";
       echo "<b>Search results from Blekko</b>";
       while($row3 = mysql_fetch_assoc( $result_blekko))
        {
	       echo "<p>".$row3['Rank'].": <a href='".$row3['Url']."'>".$row3['Title']."</a><br /><i>".$row3['Description']."</i><br /><span style='font-size: 10pt;'>".$row3['DisplayUrl']."</span></p>"; // display everything
        }
?>
