<?php
    /* Function takes the initial query when called send that query to each API the 
     * inserts the returned values from each API into the tables Bing_rewrite, Yahoo_rewrite and
     * Blekko_rewrite. The tf-idf values are calculated the top 'n' terms are selected and appended to
     * the end of the initial query. This new query is then sent back to the API's and the usual tables are
     * then populated and used as normal.
     * Note: Bing and Yahoo can handle larger amount of terms submitted to
     * them in a string, Blekko however fails if the number of terms is greater than 5, which results
     * in it returning NULL.   ******/  
    function Query_Rewrite($initial_query)
    {
      
      
      $search_api = $initial_query;
      include_once('functions.php');
      include_once('preprocessing.php');
     
     /*********** Start database connection  ********/ 
     $con = mysql_connect("localhost","root","BandWat250");
     if (!$con)
     {  
       die('Could not connect: ' . mysql_error());
     }

     mysql_select_db("Sentinel_search", $con);
     
      /******** Empty previous table data ************/
      $sql_trun1 = "TRUNCATE TABLE Bing ";
      mysql_query($sql_trun1);
      $sql_trun2 = "TRUNCATE TABLE Yahoo ";
      mysql_query($sql_trun2); 
      $sql_trun3 = "TRUNCATE TABLE Blekko ";
      mysql_query($sql_trun3);
       
        
      $sql_trun4 = "TRUNCATE TABLE Bing_rewrite ";
      mysql_query($sql_trun4);
      $sql_trun5 = "TRUNCATE TABLE Yahoo_rewrite ";
      mysql_query($sql_trun5); 
      $sql_trun6 = "TRUNCATE TABLE Blekko_rewrite ";
      mysql_query($sql_trun6);
      
                    
     
     /**************************** Query Rewrite Bing *************************************/
     
     function Bing_Rewrite($search_api)
     {
     /****************     Initial Query Bing API     *************/
         $get1 = file_get_contents("http://api.bing.net/json.aspx?AppId=C53F07042DA2CE02962981A892641469BDDD6EA6&Query=".urlencode($search_api)."&Sources=Web&Market=en-US&web.count=50");
         $decode1 = json_decode($get1, TRUE); // TRUE for in array format
    
              foreach($decode1['SearchResponse']['Web']['Results'] as $res) 
                { // foreach loop, to loop through each array value (result) as $res
                  $Description = mysql_real_escape_string($res['Description']);
                  mysql_query("INSERT INTO Bing_rewrite (Description)
                            VALUES ('$Description')");
                }  
                
     
     $query_bing="SELECT * FROM Bing_rewrite";
     $result_bing=mysql_query($query_bing);
     
     $i = 0;
     while($row = mysql_fetch_assoc($result_bing))
       {
	      
	      $Description = $row['Description'];
	      $bing_rewrite[$i] = $Description;
	      $i++;
        }
        
      $k=0;
      for($k=0; $k<50; $k++)
      {
		 $preprocessing[$k] = ereg_replace("[^A-Za-z0-9 _]", "", $bing_rewrite[$k]);
		 $preprocessings[$k] = stopwords($preprocessing[$k]);
		 
	  }
	  
   /************** TF-IDF   *************************/

   $data = $preprocessings;
   $i=0;
     $num_docs = count($data);       // 20 lines
	 
	 $num_word_each_doc = 0;
	 
	for($i=0; $i<$num_docs; $i++)
	 {
	   $each_doc[$i] = explode(" ",$data[$i]);
	 }

     $counter_all = 0;
	 for($i=0; $i < $num_docs; $i++)
	 {
		 $num_word_each_doc = count($each_doc[$i]);
	
		 for($j=0; $j<$num_word_each_doc; $j++)
		 {
		        $words[] = strtolower(trim($each_doc[$i][$j]));
		       
		 }
		 
	 }
      
    
    $ind_words = array_unique($words);
     $num_ind = count($ind_words);
     
		 
		 foreach($data as $dat)
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
        
        
        $max_count = reset($count_ind_words_highest); 
       	
	     foreach($data as $dat)
		 {
			      $trimmed = trim($dat);
		        $words_in_doc = explode(" ",$trimmed);
		    $ind_words_doc = array_unique($words_in_doc);
		    $ind_wor_line[] = implode(" ", $ind_words_doc);
		 
		 }
	  
	  
	// var_dump($count_ind_words);
	  
      	 
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
	
	//  var_dump($doc_freq_per_term);
	 
     $total_docs = $num_docs;
     $tf = $count/$max_count;
     
     
     foreach ($ind_words as $ind_wrd)
     {
		 $tf_idf[$ind_wrd] =($count_ind_words[$ind_wrd]/$max_count) * log($total_docs/$doc_freq_per_term[$ind_wrd]);
		 
     }
     
     
     arsort($tf_idf);
     $top_ten =  array_slice($tf_idf, 0, 7);
   //  var_dump($top_ten);
     $i=0;
     foreach($top_ten as $key => $value)
     {
		 $query_rewrite[$i] = $key;
		 $i++;
	 }
	 
	 // echo "<br>";
	  // var_dump($query_rewrite);
	  $append = trim(implode(" ", $query_rewrite));
	// echo $append;
	 echo "<b>Rewritten query from Bing:</b> "; 
	  $search_append = "".$search_api."  ".$append."";
	  echo $search_append;
	     /****************      Bing API     *************/
         $get1 = file_get_contents("http://api.bing.net/json.aspx?AppId=C53F07042DA2CE02962981A892641469BDDD6EA6&Query=".urlencode($search_append)."&Sources=Web&Market=en-US&web.count=50");
         $decode1 = json_decode($get1, TRUE); // TRUE for in array format
         
         $i = 0;  // incremental variable for search result numbering
         $k = 51; // decremental variable for assigning decending scores 
              foreach($decode1['SearchResponse']['Web']['Results'] as $res) 
                { // foreach loop, to loop through each array value (result) as $res
                 $i++; // incrementation
                 $k--; 
                 
                  $Rank = $i;
                  $Score = $k;
                  $Url = mysql_real_escape_string($res['Url']);
                  $Title = mysql_real_escape_string($res['Title']);
                  $Description = mysql_real_escape_string($res['Description']);
                  $DisplayUrl = mysql_real_escape_string($res['DisplayUrl']);

                   mysql_query("INSERT INTO Bing ( Rank, Url, Title, Description, DisplayUrl, Score)
                            VALUES ('$Rank', '$Url', '$Title', '$Description', '$DisplayUrl', '$Score')");
                } 

    } // End function Bing_Rewrite
    
    
    /**************************** Query Rewrite Yahoo *************************************/
     
     function Yahoo_Rewrite($search_api)
     {
     /****************     Initial Query Yahoo API     *************/
        $search_apis = urlencode($search_api);  
          $yhost = 'http://www.entireweb.com';
          $apikey = '20588cc95b7c11ce42d3be0fabc82be0';
          $url = $yhost.'/ysearch/web/v1/'.$search_apis.'?appid='.$apikey.'&format=json&lang=en&region=us&count=50&style=raw';

          $get2 = file_get_contents($url);
          $decode2 = json_decode($get2, TRUE);
          
          $j=0;
              foreach($decode2['ysearchresponse']['resultset_web'] as $res2) 
                { // foreach loop, to loop through each array value (result) as $res
                  $j++;
                  
                  $Description2 = mysql_real_escape_string($res2['abstract']);

                   mysql_query("INSERT INTO Yahoo_rewrite (Description)
                            VALUES ('$Description2')");
                            if( $j == 50)
                            {
								break;
							}
                }  
     
     $query_yahoo = "SELECT * FROM Yahoo_rewrite";
     $result_yahoo = mysql_query($query_yahoo);
     
     $i = 0;
     while($row = mysql_fetch_assoc($result_yahoo))
       {
	      
	      $Description = $row['Description'];
	      $yahoo_rewrite[$i] = $Description;
	      $i++;
        }
        
  
      
      
      $k=0;
      for($k=0; $k<50; $k++)
      {
		 $preprocessing[$k] = ereg_replace("[^A-Za-z0-9 _]", "", $yahoo_rewrite[$k]);
		 $preprocessings[$k] = stopwords($preprocessing[$k]);
		 
	  }
	  
   /************** TF-IDF   *************************/

   $data = $preprocessings;
   $i=0;
     $num_docs = count($data);       // 20 lines
	 
	 $num_word_each_doc = 0;
	 
	for($i=0; $i<$num_docs; $i++)
	 {
	   $each_doc[$i] = explode(" ",$data[$i]);
	 }

     $counter_all = 0;
	 for($i=0; $i < $num_docs; $i++)
	 {
		 $num_word_each_doc = count($each_doc[$i]);
	
		 for($j=0; $j<$num_word_each_doc; $j++)
		 {
		        $words[] = strtolower(trim($each_doc[$i][$j]));
		       
		 }
		 
	 }
      
    
    $ind_words = array_unique($words);
     $num_ind = count($ind_words);
     
		 
		 
		 
		 foreach($data as $dat)
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
        
        
        $max_count = reset($count_ind_words_highest); 
       	
	 foreach($data as $dat)
		 {
			      $trimmed = trim($dat);
		        $words_in_doc = explode(" ",$trimmed);
		    $ind_words_doc = array_unique($words_in_doc);
		    $ind_wor_line[] = implode(" ", $ind_words_doc);
		 
		 }
	  
	  
	// var_dump($count_ind_words);
	  
      	 
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
		// echo "<br>";
       // echo "<br>";
	//  var_dump($doc_freq_per_term);
	 
     $total_docs = $num_docs;
     $tf = $count/$max_count;
     
     
     foreach ($ind_words as $ind_wrd)
     {
		 $tf_idf[$ind_wrd] =($count_ind_words[$ind_wrd]/$max_count) * log($total_docs/$doc_freq_per_term[$ind_wrd]);
		 
     }
     
     
     arsort($tf_idf);
     $top_ten =  array_slice($tf_idf, 0, 5);
   //  var_dump($top_ten);
     $i=0;
     foreach($top_ten as $key => $value)
     {
		 $query_rewrite[$i] = $key;
		 $i++;
	 }
	 
	 // echo "<br>";
	 // var_dump($query_rewrite);
	  $append = trim(implode(" ", $query_rewrite));
	//  echo $append;
	  echo "<br>";
	  echo "<b>Rewritten query from Yahoo: </b>";
	  $search_append = "".$search_api."  ".$append."";
	  echo $search_append;
	 
	   
           /****************      Yahoo API    *************/ 
          $search_apis = urlencode($search_append);  
          $yhost = 'http://www.entireweb.com';
          $apikey = '20588cc95b7c11ce42d3be0fabc82be0';
          $url = $yhost.'/ysearch/web/v1/'.$search_apis.'?appid='.$apikey.'&format=json&lang=en&region=us&count=50&style=raw';

          $get2 = file_get_contents($url);
          $decode2 = json_decode($get2, TRUE);
          
          $j = 0;  // incremental variable for search result numbering
          $l = 51; // decremental variable for assigning decending scores   
              foreach($decode2['ysearchresponse']['resultset_web'] as $res2) 
                { // foreach loop, to loop through each array value (result) as $res
                 $j++; // incrementation
                 $l--;
                 $Score2 = $l;
                  $Rank2 = $j;
                  $Url2 = mysql_real_escape_string($res2['url']);
                  $Title2 = mysql_real_escape_string($res2['title']);
                  $Description2 = mysql_real_escape_string($res2['abstract']);
                  $DisplayUrl2 = mysql_real_escape_string($res2['dispurl']);

                   mysql_query("INSERT INTO Yahoo ( Rank, Url, Title, Description, DisplayUrl, Score)
                            VALUES ('$Rank2', '$Url2', '$Title2', '$Description2', '$DisplayUrl2', '$Score2')");
                  if( $j == 50)
                  {
					  break;
				  }
                
                }  

    } // End function Yahoo_Rewrite
    
    
    
    
    
     function Blekko_Rewrite($search_api)
     {
     /****************     Initial Query Blekko API     *************/
    $get3 = file_get_contents("http://blekko.com/?q=".urlencode($search_api)."+/ps=50+/json&auth=b58f6ba2");
         $decode3 = json_decode($get3, TRUE); // TRUE for in array format
         
              foreach($decode3['RESULT'] as $res) 
                { // foreach loop, to loop through each array value (result) as $res
              
                $Description = strip_tags(mysql_real_escape_string($res['snippet']));
                mysql_query("INSERT INTO Blekko_rewrite (Description)
                            VALUES ('$Description')");
                }       
                
     
     $query_blekko="SELECT * FROM Blekko_rewrite";
     $result_blekko=mysql_query($query_blekko);
     
     $i = 0;
     while($row = mysql_fetch_assoc($result_blekko))
       {
	      
	      $Description = $row['Description'];
	      $blekko_rewrite[$i] = $Description;
	      $i++;
        }
        
  
      
      
      $k=0;
      for($k=0; $k<50; $k++)
      {
		 $preprocessing[$k] = ereg_replace("[^A-Za-z0-9 _]", "", $blekko_rewrite[$k]);
		 $preprocessings[$k] = stopwords($preprocessing[$k]);
		 
	  }
	  
   /************** TF-IDF   *************************/

   $data = $preprocessings;
   $i=0;
     $num_docs = count($data);       // 20 lines
	 
	 $num_word_each_doc = 0;
	 
	for($i=0; $i<$num_docs; $i++)
	 {
	   $each_doc[$i] = explode(" ",$data[$i]);
	 }

     $counter_all = 0;
	 for($i=0; $i < $num_docs; $i++)
	 {
		 $num_word_each_doc = count($each_doc[$i]);
	
		 for($j=0; $j<$num_word_each_doc; $j++)
		 {
		        $words[] = strtolower(trim($each_doc[$i][$j]));
		       
		 }
		 
	 }
      
    
    $ind_words = array_unique($words);
     $num_ind = count($ind_words);
     
		 
		 
		 
		 foreach($data as $dat)
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
        
        
        $max_count = reset($count_ind_words_highest); 
       	
	 foreach($data as $dat)
		 {
			      $trimmed = trim($dat);
		        $words_in_doc = explode(" ",$trimmed);
		    $ind_words_doc = array_unique($words_in_doc);
		    $ind_wor_line[] = implode(" ", $ind_words_doc);
		 
		 }
	  
	  
	// var_dump($count_ind_words);
	  
      	 
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
	//	 echo "<br>";
     //   echo "<br>";
	//  var_dump($doc_freq_per_term);
	 
     $total_docs = $num_docs;
     $tf = $count/$max_count;
     
     
     foreach ($ind_words as $ind_wrd)
     {
		 $tf_idf[$ind_wrd] =($count_ind_words[$ind_wrd]/$max_count) * log($total_docs/$doc_freq_per_term[$ind_wrd]);
		 
     }
     
     
     arsort($tf_idf);
     $top_ten =  array_slice($tf_idf, 0, 2);
   //  var_dump($top_ten);
     $i=0;
     foreach($top_ten as $key => $value)
     {
		 $query_rewrite[$i] = $key;
		 $i++;
	 }
	 
	 
	  // var_dump($query_rewrite);
	  $append = trim(implode(" ", $query_rewrite));
	echo "<br>";
	 echo "<b>Rewritten query from Blekko: </b>";
	  $search_append = "".$search_api."  ".$append."";
	  echo $search_append;
	   echo "<br>";   
	   echo "<br>"; 
	      $get3 = file_get_contents("http://blekko.com/?q=".urlencode($search_append)."+/ps=50+/json&auth=b58f6ba2");
         $decode3 = json_decode($get3, TRUE); // TRUE for in array format
         
         $i = 0;  // incremental variable for search result numbering
         $k = 51; // decremental variable for assigning decending scores 
              foreach($decode3['RESULT'] as $res) 
                { // foreach loop, to loop through each array value (result) as $res
                 $i++; // incrementation
                 $k--; 
                 
                  $Rank = $i;
                  $Score = $k;
                  $Url = strip_tags(mysql_real_escape_string($res['url']));
                  $Title = strip_tags(mysql_real_escape_string($res['url_title']));
                  $Description = strip_tags(mysql_real_escape_string($res['snippet']));
                  $DisplayUrl = strip_tags(mysql_real_escape_string($res['display_url']));

                   mysql_query("INSERT INTO Blekko ( Rank, Url, Title, Description, DisplayUrl, Score)
                            VALUES ('$Rank', '$Url', '$Title', '$Description', '$DisplayUrl', '$Score')");
                }       

    } // End function Blekko_Rewrite
    
    $result1 = Bing_Rewrite($search_api);
    $result2 = Yahoo_Rewrite($search_api);
    $result3 = Blekko_Rewrite($search_api);
    } // end function Query_Rewrite.
?>
