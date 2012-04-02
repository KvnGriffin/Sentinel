<?php 
 /** finction takes  a string as it's input value and then sends it to each API 
  * the returned JSON file is then formatted into an array using  PHP function json_decode.
  * 
  * Notes: each JSON file from each API is structured slightly differently so the foreach loops 
  * are different to a small degree. The resulting decoded arrays are then inserted into each 
  * corresponding table in the database. Each table is truncated first(contents deleted) so that old
  * info is removed before new results are inserted for each query. Blekko returned it's text with HTML <strong> 
  * tags surronding text so PHP function 'strip_tags' is used on each result before inserting into database.
  * mysql_real_escape_string is used also as it escapes special characters(\n, \r, etc) in a string for use in an 
  * SQL statement, otherwise Apache would throw an error and no info would be inserted into database.  */ 
      
    function API_Module($Query_request)
    {
	  $search_api = $Query_request;
		 
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
        
        /****************      Bing API     *************/
         $get1 = file_get_contents("http://api.bing.net/json.aspx?AppId=C53F07042DA2CE02962981A892641469BDDD6EA6&Query=".urlencode($search_api)."&Sources=Web&Market=en-US&web.count=50");
         $decode1 = json_decode($get1, TRUE); // TRUE for in array format
       //  print_r($decode1);
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
                
           
           /****************      Entireweb API    *************/ 
           
           // dj0yJmk9VTR6WE93bmM4bkVhJmQ9WVdrOWRWbExlbXBaTmpRbWNHbzlPVFEwTmpnNU9UWXkmcz1jb25zdW1lcnNlY3JldCZ4PTVl
          $search_apis = urlencode($search_api);  
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
                 if($j == 50)
                 {
					 break;
				 }
               
                }  
           
        /****************      Blekko API     *************/
         $get3 = file_get_contents("http://blekko.com/?q=".urlencode($search_api)."+/ps=50+/json&auth=b58f6ba2");
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
                
	} // End function API_Module
	
?>                
