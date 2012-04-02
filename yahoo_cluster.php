<html>
	
<?php
     
     include_once('functions.php');
     echo " CLUSTERED RESULTS ENTIREWEB";
     echo "<br>";
     echo "<br>";
    
  /************************** Prepare the data *****************************/  
    
     /*********** Start database connection  ********/ 
     $con = mysql_connect("localhost","root","BandWat250");
     if (!$con)
     {  
       die('Could not connect: ' . mysql_error());
     }

     mysql_select_db("Sentinel_search", $con);
     
     $query_yahoo = "SELECT * FROM Yahoo";
     $result_yahoo = mysql_query($query_yahoo);
     
    
   /***** Pull data into array $yahoo as key => value pairs($url2 => $description) */
       while($row2 = mysql_fetch_assoc( $result_yahoo))
        {
	       $url2 =$row2['Url'];
	       $description = $row2['Description'];
	       $yahoo[$url2] = $description;
        }
    
     // var_dump($bing);
      
   /******* Remove all punctuation(ereg_replace, replaces anything that is'nt a upper/lower case letter
    * with nothing) and stopwords, did'nt bother stemming words, 
    * hoped it might help
    * with speed*/ 
    $punctuation = array(); 
    $collection = array();
    foreach($yahoo as $url => $string)
    {
	  $punctuation[$url] = ereg_replace("[^A-Za-z0-9 _]", "", $yahoo[$url]);
    }
    
    foreach($punctuation as $url => $string)
    {
		$collection[$url] = stopwords($punctuation[$url]);
	}
	  
	
	/******************** Start the clustering ***************************/

/*** Array $words contains all words(lowercase and trimmed of any white space in $collection   ***/
  $each_url = array();
  $words = array();
  foreach($collection as $url => $strin)
	{
	  $each_url = explode(" ", strtolower($strin));
		  foreach($each_url as $ul => $wrd)
		   {
		     $words[] = strtolower(trim($each_url[$ul]));
		   }
	}
	
/**** array_unique removes all duplicates from $word ****/
    $ind_words = array();
    $ind_words = array_unique($words);
	$num_ind = count($ind_words);
  
    foreach($collection as $dat)
	{
		$trimmed = strtolower(trim($dat));
		$words_in_doc = explode(" ",$trimmed);
		$ind_words_doc = array_unique($words_in_doc);
		     
		  foreach($ind_words_doc as $indwrds)
		  {
            foreach($words_in_doc as $wrds) 		 
		    {
			 if (strcasecmp(trim($indwrds), trim($wrds)) == 0)
			 $count_ind_words[$indwrds]++;
			}
	      }
	}
	
	$count_ind_words_highest = $count_ind_words;
		arsort($count_ind_words_highest);
      //  echo "Highest count: ";
        
    $max_count = reset($count_ind_words_highest); 
       // echo $max_count;
	 
	 /**************************** Term frequencies ***********************
	  * counts number of occurencies of each individual word associated with each url  */
	
	foreach($collection as $col => $st)
	{
		$string = strtolower($st);
	    $url1 = explode(" ",$string);
	    foreach($url1 as $u)
	    {
	    foreach($ind_words as $indwrds)
	    {
		     if (strcasecmp(trim($indwrds), trim($u)) == 0)
			 $count_ind_words_url[$col][$indwrds]++;
			 
	    }
	    }		
	}
	
	/********************* Document frequencies *********************************
	 * counts the number of documents in which a word appears ********/
		foreach($collection as $dat)
		{
			      $trimmed = trim($dat);
		        $words_in_doc = explode(" ",$trimmed);
		    $ind_words_doc = array_unique($words_in_doc);
		    $ind_wor_line[] = implode(" ", $ind_words_doc);
		 
		}
		
		 foreach($ind_wor_line as $iwl)
		 {
			$trimmed = strtolower(trim($iwl));
		    $words_in_doc = explode(" ",$trimmed);
		    $ind_words_doc = array_unique($words_in_doc);
		     
		  foreach($ind_words_doc as $indwrds)
		  {
           foreach($words_in_doc as $wrds) 		 
		   {
			 if (strcasecmp(trim($indwrds), trim($wrds)) == 0)
			 $doc_freq_per_term[$indwrds]++;
			 
		   }
	      }
		 }
		 
		 $num_of_urls = count($collection); // number of url's to be used in idf calculation.
	  
	  /***************** tf-idf ********************************/
	 //  echo "<br>";
	foreach($count_ind_words_url as $url => $wrd)
	{
	
	   foreach ( $wrd as $key => $val)
	   {  
			$count_ind_words_url2[$url][$key] = $count_ind_words_url[$url][$key] * log($num_of_urls/$doc_freq_per_term[$key],2);
	   }
	}
	
	/* In order to be able to keep track of each member as it is added to a centroid we must add an 
	 * extra dimension to the url => tf-idf scores. This is done by creating a new array called $coll_tf_idf
	 * so the new array will have the structure [url][url] => tf_idf(score) */
	$coll_tf_idf = array();
	  foreach ($collection as $url => $string)
	  {
		  $coll_tf_idf[$url][$url] = $count_ind_words_url2[$url];
	  }
	// echo "This is: coll_tf_idf ";
	// echo "<br>";
	// var_dump($coll_tf_idf);
	 
  
    
    /******************************* Initialize Centroids *************************
     *  Choose $kmeans number of random initial centroids, which gives an array called $centroids
     * which is then placed into 'centroid' when array $clusters is created */
	 
	 $kmeans = 7;
	 $coll_tf_idf_rand = array();
	 $coll_tf_idf_rand = $coll_tf_idf;
	 for($i=0; $i<$kmeans; $i++)
	 {
		 $rand[$i] = array_rand($coll_tf_idf_rand);
		 $centroids[$i] = $coll_tf_idf_rand[$rand[$i]][$rand[$i]]; 
	 }

      
     $clusters= array();  // $clusters is each cluster
	 $k=0;
	 
	for($k=0;$k<$kmeans;$k++)
	{
	  $clusters[$k] = array( 'centroid' => $centroids[$k],
	                    'members' => array()
	                    );
    }
	
	/**************************** assign url's to centroid members ************
	 * K-Means runs here $iterations number of times. Note: it usually did'nt change the 
	 * cluster members beyond 1-2 iterations I due to small data set.*/         
	$iterations = 5;
	
	$q=0;
	for($q=0; $q<$iterations; $q++)
	{ 
	    $index = 0;
		$cluster_index= 0;
		foreach($coll_tf_idf_rand as $url)
		{
			foreach($url as $url2)
			{
			 $max_sim=0;
			 
			    for($cluster_index=0; $cluster_index<$kmeans;$cluster_index++)
			    {
				 $buffer = Cosine_sim($clusters[$cluster_index]['centroid'], $url2);
				 if($buffer > $max_sim)
				 {
				   $max_sim = $buffer;
				   $index = $cluster_index;
				 }
				
			 
			    }
		    }
			 $clusters[$index]['members'][] = $url;
			 $clusters[$index]['centroid'] = RecalcCentroid($clusters[$index]);
			  
			  if($q != $iterations-1)
			  {
			  $clusters[$index]['members'] = NULL;
			  
		      }
		}
    }
    
   /*************************** Cluster names use word with highest tf-idf score for each cluster member
   *  array $highest-term holds the name with each ith element corresponding to it's cluster number  */
   
   $max_term = array();
   $highest_term = array();
   for($i=0;$i<$kmeans;$i++)
   {
   foreach($clusters[$i]['members'] as $url => $string)
   {
	   foreach($string as $word => $value)
	   {
		   $max_term[$i] =$value;
	   }
   }
     arsort($max_term[$i]);
     $highest_term[$i] = ucfirst(key($max_term[$i]));
   }  
?>	

<!-- Could not figure out a way to have each cluster name as a clickable link on the left and display
 only those members on the right. In the end I went for a less appealing method of assigning each set
 of results manually to a div tag and then if a cluster name is clicked on it brings the user to that 
 specific div on the page. Note: if the K in K-Means above is changed then this would also have to be 
 rewritten, which is not desirable, but I think thats more to do with my lack of HTML skills and 
 trying to find a solution to the display(also PHP is server-side and HTML is browser-side so not 
 sure how it would work).  -->  
	
<div id="Menu">
<?php 
       
     echo "<a href='#zero'>".$highest_term[0]."</a>";
     echo "<br>";
     echo "<a href='#one'>".$highest_term[1]."</a>";
     echo "<br>";
     echo "<a href='#two'>".$highest_term[2]."</a>";
     echo "<br>";
     echo "<a href='#three'>".$highest_term[3]."</a>";
     echo "<br>";
     echo "<a href='#four'>".$highest_term[4]."</a>";
     echo "<br>";
	 echo "<a href='#five'>".$highest_term[5]."</a>";
     echo "<br>";
     echo "<a href='#six'>".$highest_term[6]."</a>";
     echo "<br>";
?>

</div>

<div id="zero">	   
  <?php 
    echo "<br>";
    echo "<big>".$highest_term[0]."</big>  <a href='#Top'> Back to Top</a>";
    for($z=0; $z<50;$z++)
    {
	foreach($clusters[0]['members'][$z] as $url => $tf)
    {
        $test = "$url";
		$clus_bing="SELECT * FROM Yahoo WHERE Url='$test'";
        $clusbing=mysql_query($clus_bing);
        $row = mysql_fetch_assoc($clusbing);
        
	     echo "<p><a href='".$row['Url']."'>".$row['Title']."</a><br /><i>".$row['Description']."</i><br /><span style='font-size: 10pt;'>".$row['DisplayUrl']."</span></p>"; // display everything
    }
    }
    ?>
</div> 

<div id="one">	   
  <?php 
  echo "<br>";
  echo "<big><b>".$highest_term[1]."</b></big><a href='#Top'> Back to Top</a>";
  for($z=0; $z<50;$z++)
    {
	foreach($clusters[1]['members'][$z] as $url => $tf)
    {
        $test = "$url";
		$clus_bing="SELECT * FROM Yahoo WHERE Url='$test'";
        $clusbing=mysql_query($clus_bing);
        $row = mysql_fetch_assoc($clusbing);
        
	     echo "<p><a href='".$row['Url']."'>".$row['Title']."</a><br /><i>".$row['Description']."</i><br /><span style='font-size: 10pt;'>".$row['DisplayUrl']."</span></p>"; // display everything
    }
    }
    ?>
</div> 

<div id="two">	   
  <?php 
  echo "<br>";
  echo "<big><b>".$highest_term[2]."</b></big><a href='#Top'> Back to Top</a>";
  for($z=0; $z<50;$z++)
    {
	foreach($clusters[2]['members'][$z] as $url => $tf)
    {
        $test = "$url";
		$clus_bing="SELECT * FROM Yahoo WHERE Url='$test'";
        $clusbing=mysql_query($clus_bing);
        $row = mysql_fetch_assoc($clusbing);
        
	     echo "<p><a href='".$row['Url']."'>".$row['Title']."</a><br /><i>".$row['Description']."</i><br /><span style='font-size: 10pt;'>".$row['DisplayUrl']."</span></p>"; // display everything
    }
    }
    ?>
</div> 

<div id="three">	   
  <?php 
  echo "<br>";
  echo "<big><b>".$highest_term[3]."</b></big><a href='#Top'> Back to Top</a>";
  for($z=0; $z<50;$z++)
    {
	foreach($clusters[3]['members'][$z] as $url => $tf)
    {
        $test = "$url";
		$clus_bing="SELECT * FROM Yahoo WHERE Url='$test'";
        $clusbing=mysql_query($clus_bing);
        $row = mysql_fetch_assoc($clusbing);
        
	     echo "<p><a href='".$row['Url']."'>".$row['Title']."</a><br /><i>".$row['Description']."</i><br /><span style='font-size: 10pt;'>".$row['DisplayUrl']."</span></p>"; // display everything
    }
    }
    ?>
</div> 

<div id="four">	   
  <?php 
  echo "<br>";
  echo "<big><b>".$highest_term[4]."</b></big><a href='#Top'> Back to Top</a>";
  for($z=0; $z<50;$z++)
    {
	foreach($clusters[4]['members'][$z] as $url => $tf)
    {
        $test = "$url";
		$clus_bing="SELECT * FROM Yahoo WHERE Url='$test'";
        $clusbing=mysql_query($clus_bing);
        $row = mysql_fetch_assoc($clusbing);
        
	     echo "<p><a href='".$row['Url']."'>".$row['Title']."</a><br /><i>".$row['Description']."</i><br /><span style='font-size: 10pt;'>".$row['DisplayUrl']."</span></p>"; // display everything
    }
    }
    ?>
</div> 

<div id="five">	   
  <?php 
  echo "<br>";
  echo "<big><b>".$highest_term[5]."</b></big><a href='#Top'> Back to Top</a>";
  for($z=0; $z<50;$z++)
    {
	foreach($clusters[5]['members'][$z] as $url => $tf)
    {
        $test = "$url";
		$clus_bing="SELECT * FROM Yahoo WHERE Url='$test'";
        $clusbing=mysql_query($clus_bing);
        $row = mysql_fetch_assoc($clusbing);
        
	     echo "<p><a href='".$row['Url']."'>".$row['Title']."</a><br /><i>".$row['Description']."</i><br /><span style='font-size: 10pt;'>".$row['DisplayUrl']."</span></p>"; // display everything
    }
    }
    ?>
</div> 

<div id="six">	   
  <?php 
  echo "<br>";
  echo "<big><b>".$highest_term[6]."</b></big><a href='#Top'> Back to Top</a>";
  for($z=0; $z<50;$z++)
    {
	foreach($clusters[6]['members'][$z] as $url => $tf)
    {
        $test = "$url";
		$clus_bing="SELECT * FROM Yahoo WHERE Url='$test'";
        $clusbing=mysql_query($clus_bing);
        $row = mysql_fetch_assoc($clusbing);
        
	     echo "<p><a href='".$row['Url']."'>".$row['Title']."</a><br /><i>".$row['Description']."</i><br /><span style='font-size: 10pt;'>".$row['DisplayUrl']."</span></p>"; // display everything
    }
    }
    ?>
</div> 

</html>	
	 

