<?php

   /*********** Start database connection  ********/ 
     $con = mysql_connect("localhost","root","BandWat250");
     if (!$con)
     {  
       die('Could not connect: ' . mysql_error());
     }

     mysql_select_db("Google_results", $con); // Select database
     
     $query_google = "SELECT * FROM dog_heat";
     $result_google = mysql_query($query_google);
       
      $google = array();
    /******* Echo all to screen in format below *********/   
    //   echo "<br>";
    //   echo "<b>Results from google</b>";
       while($row = mysql_fetch_assoc( $result_google))
        {
	       $google[] = $row['Url']; 
        }
     
  //   print_r($google);
     
      /***** Close connection **********/
     mysql_close($con);
    
    
    /*********** Start database connection  ********/ 
     $cons = mysql_connect("localhost","root","BandWat250");
     if (!$cons)
     {  
       die('Could not connect: ' . mysql_error());
     }

   
     
     mysql_select_db("Sentinel_search", $cons); // Select database
     
      /******* Select all info from each table ***********/
     $query_yahoo="SELECT * FROM Yahoo";
     $result_yahoo=mysql_query($query_yahoo);
     
     $query_bing="SELECT * FROM Bing";
     $result_bing=mysql_query($query_bing);
     
     $query_blekko = "SELECT * FROM Blekko";
     $result_blekko = mysql_query($query_blekko);
       
      $yahoo = array();
    /******* Echo all to screen in format below *********/   
     //  echo "<br>";
      // echo "<b>Results from Yahoo</b>";
       while($row = mysql_fetch_assoc( $result_yahoo))
        {
	       $yahoo[] = $row['Url']; // display everything
        }
    //    print_r($yahoo);
        
        $bing = array();
     //  echo "<br>";
     //  echo "<br>";
      // echo "<b>Results from Bing</b>";
       while($row2 = mysql_fetch_assoc( $result_bing))
        {
	       $bing[] =  $row2['Url']; 
	    }
       
      // print_r($bing);
       $blekko = array();
     //  echo "<br>";
     //  echo "<br>";
       //echo "<b>Search results from Blekko</b>";
       while($row3 = mysql_fetch_assoc( $result_blekko))
        {
	       $blekko[] = $row3['Url']; // display everything
        }
   
     //  print_r($blekko);
       
     /***** Close connection **********/
     mysql_close($cons);
     
     echo "<br>";
     echo "<br>";
     $num_docs_col = count($google);
     echo "Number in Google: ";
     echo $num_docs_col;
     echo "<br>";
     echo "<br>";
    
    
     /*************** Yahoo & google precision and recall *************************/
     $num_docs_ret_yahoo = count($yahoo);
     echo "Number in Yahoo: ";
     echo $num_docs_ret_yahoo;
     $intersection_yahoo = 0;
     $sum_yahoo=0;
     $prec_yahoo=0;
     $ave_prec_yahoo=0;
     $num_elementyahoo=0;
     for($i=0; $i<$num_docs_ret_yahoo; $i++)
     {
		 for($j=0; $j<$num_docs_ret_yahoo; $j++)
		 {
		  if(strcmp(trim($yahoo[$i]), trim($google[$j])) == 0)
		  {
		  $intersection_yahoo++;
		  
		    $num_elementyahoo = $i+1;

			 $prec_yahoo = $intersection_yahoo/$num_elementyahoo;
			
			 $sum_yahoo += $prec_yahoo;
		 }
	     }
     }
    
    echo"<br>";
     echo "Intersection  Yahoo and google: ";
     echo $intersection_yahoo;
     echo "<br>";
     echo " Sum yahoo: ";
     echo $sum_yahoo;
     $ave_prec_yahoo = $sum_yahoo/$intersection_yahoo;
     echo "<br>";
     echo "PRECISION/RECALL: ";
    echo $intersection_yahoo/$num_docs_ret_yahoo;
    echo "<br>";
     echo " AVERAGE PRECISION YAHOO: ";
     echo $ave_prec_yahoo;
     
     echo "<br>";
     echo "<br>";
     
     /*************** Bing & google precision and recall *************************/
     $num_docs_ret_bing = count($bing);
     echo "Number in Bing: ";
     echo $num_docs_ret_bing;
     $i =0;
     $j=0;
     $intersection_bing = 0;
     for($i=0; $i<$num_docs_ret_bing; $i++)
     {
		 for($j=0; $j<$num_docs_ret_bing; $j++)
		 {
		  if(strcmp(trim($bing[$i]), trim($google[$j])) == 0)
		  {
		  $intersection_bing++;
		
		    $num_elementbing = $i+1;
			 $prec_bing = $intersection_bing/$num_elementbing;
		     $sum_bing += $prec_bing;
		 }
	     }
     }
     echo "<br>";
      echo "Intersection bing and google ";
     echo $intersection_bing;
	 echo "<br>"; 
     echo " Sum bing: ";
     echo $sum_bing;
     $ave_prec_bing = $sum_bing/$intersection_bing;
     echo "<br>";
      echo "PRECISION/RECALL: ";
    echo $intersection_bing/$num_docs_ret_bing;
    echo "<br>";
     echo " AVERAGE PRECISION BING: ";
     echo $ave_prec_bing;
     echo "<br>";
     
      echo "<br>";
     echo "<br>";
      
      
      
     /*************** Blekko & google precision and recall *************************/
     $num_docs_ret_blekko = count($blekko);
     echo "Number in Blekko: ";
     echo $num_docs_ret_blekko;
     echo "<br>";
     $intersection_blekko = 0;
     $sum_blekko=0;
     $prec_blekko=0;
     $ave_prec_blekko=0;
     $num_elementblekko=0;
     for($i=0; $i<$num_docs_ret_blekko; $i++)
     {
		 for($j=0; $j<$num_docs_ret_blekko; $j++)
		 {
		  if(strcmp(trim($blekko[$i]), trim($google[$j])) == 0)
		  {
		  $intersection_blekko++;
		 
			 $num_elementblekko = $i+1;
	         $prec_blekko = $intersection_blekko/$num_elementblekko;
			 $sum_blekko += $prec_blekko;
			 }
	     }
     }
     
     echo "Intersection blekko and google: ";
     echo $intersection_blekko;
     echo "<br>";
     echo " Sum blekko: ";
     echo $sum_blekko;
     $ave_prec_blekko = $sum_blekko/$intersection_blekko;
     echo "<br>";
          echo "PRECISION/RECALL: ";
    echo $intersection_blekko/$num_docs_ret_blekko;
    echo "<br>";
     echo " AVERAGE PRECISION BLEKKO: ";
     echo $ave_prec_blekko;
     echo "<br>";
     echo "<br>";
     
    /************************* Aggregated Results ************************/
    
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
      echo "<br>";
    
    /* Associative Array $yahoo contains Url and Score as key => value */ 
        while($row = mysql_fetch_assoc( $result_yahoo)) 
        {
	      $url = $row['Url'];
	      $score = $row['Score'];
	      $yahoo_agg[$url] = $score;
        }
    //      echo "number in Yahoo agg: ";
    //    echo count($yahoo_agg);
     //   echo "<br>";
       
    /* Associative Array $bing contains Url and Score as key => value */ 
        while($row2 = mysql_fetch_assoc( $result_bing))
        {
	       $url2 =$row2['Url'];
	       $score2 = $row2['Score'];
	       $bing_agg[$url2] = $score2;
        }
   //   echo "number in Bing agg: ";
    //    echo count($bing_agg);
    //    echo "<br>";
    
    
    /* Associative Array $blekko contains Url and Score as key => value */ 
        while($row3 = mysql_fetch_assoc( $result_blekko))
        {
	      $url3 = $row3['Url'];
	      $score3 = $row3['Score'];
	      $blekko_agg[$url3] = $score3;
        } 
     //  echo "number in Blekko agg: ";
   // //    echo count($blekko_agg);
    //    echo "<br>";
    
    /* aggregate bing and yahoo into a new array called $temp */
    $temp_agg = Borda($blekko_agg, $yahoo_agg);  
    /* aggregate $temp and blekko into a new array called $temp2 to give 
     * an aggregates array for all 3 search engines */

    $temp2_agg = Borda($bing_agg, $temp_agg);
    
    /* Sort new array by descending values */  
    arsort($temp2_agg);
    
    /* "pop" last element from array $temp2 as last element is always " " => NULL
     * due to final iteration stopping at end */
    $end_element = array_pop($temp2_agg);  //  var_dump($temp2); // used for testing
     
   //    echo "temp2_agg: ";
     //  echo count($temp2_agg);
    //   echo "<br>"; 
     
       
       
       
         $aggregated = array();
       
       
        foreach($temp2_agg as $url4 => $score3)
        {
	      $aggregated[] = $url4;
        }
        
      //  print_r($aggregated);
    //    echo "<br>";
   //     echo "<br>";
      $aggregated_50 = array();   // top 50 to match google
      for($i=0; $i<50; $i++)
      {
		$aggregated_50[$i]= $aggregated[$i];  
	  }
     
      
           /*************** Aggregated & google precision and recall *************************/
     $num_docs_ret_agg = count($aggregated_50);
     echo "Number in Aggregated: ";
     echo $num_docs_ret_agg;
     echo "<br>";
     $intersection_agg = 0;
     $sum_agg = 0;
     $prec_agg = 0;
     $ave_prec_agg = 0;
     $num_elementagg = 0;
     for($i=0; $i<$num_docs_ret_agg; $i++)
     {
		 for($j=0; $j<$num_docs_ret_agg; $j++)
		 {
		  if(strcmp(trim($aggregated_50[$i]), trim($google[$j])) == 0)
		  {
		  $intersection_agg++;
		 
			 $num_elementagg = $i+1;
	         $prec_agg = $intersection_agg/$num_elementagg;
			 $sum_agg += $prec_agg;
			 }
	     }
     }
     
     echo "Intersection Aggregated and google: ";
     echo $intersection_agg;
     echo "<br>";
     echo " Sum Aggregated: ";
     echo $sum_agg;
     $ave_prec_agg = $sum_agg/$intersection_agg;
     echo "<br>";
          echo "PRECISION/RECALL: ";
    echo $intersection_agg/$num_docs_ret_agg;
    echo "<br>";
     echo " AVERAGE PRECISION AGGREGATED: ";
     echo $ave_prec_agg;
     echo "<br>";
     echo "<br>";
?>
