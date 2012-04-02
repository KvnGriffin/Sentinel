<?php

    include_once('functions.php'); // used to include function Borda

    /*********** Start database connection  ********/ 
     $con = mysql_connect("localhost","root","BandWat250"); 
     if (!$con)
     {  
       die('Could not connect: ' . mysql_error());
     }
   
    /* Select results from database tables */   
     mysql_select_db("Sentinel_search", $con);

     $query_yahoo="SELECT * FROM Yahoo";
     $result_yahoo=mysql_query($query_yahoo);
     
     $query_bing="SELECT * FROM Bing";
     $result_bing=mysql_query($query_bing);
     
     $query_blekko="SELECT * FROM Blekko";
     $result_blekko=mysql_query($query_blekko);
     
    echo "<b>Aggregated results</b>";
    
    /* Associative Array $yahoo contains Url and Score as key => value */ 
        while($row = mysql_fetch_assoc( $result_yahoo)) 
        {
	      $url = $row['Url'];
	      $score = $row['Score'];
	      $yahoo[$url] = $score;
        }
       
    /* Associative Array $bing contains Url and Score as key => value */ 
        while($row2 = mysql_fetch_assoc( $result_bing))
        {
	       $url2 =$row2['Url'];
	       $score2 = $row2['Score'];
	       $bing[$url2] = $score2;
        }
    
    /* Associative Array $blekko contains Url and Score as key => value */ 
        while($row3 = mysql_fetch_assoc( $result_blekko))
        {
	      $url3 = $row3['Url'];
	      $score3 = $row3['Score'];
	      $blekko[$url3] = $score3;
        } 
        
    
    /* aggregate bing and yahoo into a new array called $temp */
    $temp = Borda($bing, $yahoo);  
    /* aggregate $temp and blekko into a new array called $temp2 to give 
     * an aggregates array for all 3 search engines */
     
    $temp2 = Borda($blekko, $temp);
    
    /* Sort new array by descending values */  
    arsort($temp2);
    
    /* "pop" last element from array $temp2 as last element is always " " => NULL
     * due to final iteration stopping at end */
    $end_element = array_pop($temp2);  //  var_dump($temp2); // used for testing
    
       
        /* Select all tables from database by the key $url4 and assign it to variable 
         * $test, then echo out results in format below in loop until end of array $temp2 is 
         * reached. A 'union' of all tables must be choosen as union joins all tables into
         * one but removes any duplicates otherwise if a url in the array $temp2 is in all tables it 
         * will print it to screen 3 times and mess up the ranks as given by iterator $t */ 
     
       
        $t = 0;
        foreach($temp2 as $url4 => $score3)
        {
	    $t++;
        $test = "$url4";
		$temp2_result = "SELECT * FROM Yahoo WHERE Url ='$test' UNION SELECT * FROM Bing WHERE Url='$test' UNION SELECT * FROM Blekko WHERE Url ='$test'";
        $temp2_results = mysql_query($temp2_result);
        $row = mysql_fetch_assoc($temp2_results);
        
	       echo "<p>".$t.": <a href='".$row['Url']."'>".$row['Title']."</a><br /><i>".$row['Description']."</i><br /><span style='font-size: 10pt;'>".$row['DisplayUrl']."</span></p>"; // display everything
        }
      
        
?>       
