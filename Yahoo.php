
<html>
<div style='text-align: center'>
     <h1>Sentinel Search</h1>
 
 <form method="post" action="Yahoo.php">
 <input type="text" name="search" size="50" />
 <input type="submit" name="submit" value="Yahoo" />
 </form>
</div>
</html>	
<?php
 $search = trim($_POST['search']);
    if($_POST['submit']) {
       if(strlen($search) == 0) {
        echo "<p>Error: empty search</p>";
                                }
else {
     $con = mysql_connect("localhost","root","BandWat250");
      if (!$con)
      {  
        die('Could not connect: ' . mysql_error());
      }

      mysql_select_db("Sentinel_search", $con);
      $sql_trun2 = "TRUNCATE TABLE Yahoo ";
      mysql_query($sql_trun2); 




          $search_apis = urlencode($search);  
          $yhost = 'http://www.entireweb.com';
          $apikey = '20588cc95b7c11ce42d3be0fabc82be0';
          $url = $yhost.'/ysearch/web/v1/'.$search_apis.'?appid='.$apikey.'&format=json&lang=en&region=us&count=50&style=raw';

          $get2 = file_get_contents($url);
          $decode2 = json_decode($get2, TRUE);
          
         $j = 0;  // incremental variable for search result numbering
          $l = 51; // decremental variable for assigning decending scores   
              foreach($decode2['ysearchresponse']['resultset_web'] as $res2) 
                { // foreach loop, to loop through each array value (result) as $res
                 echo "<p>".$res2['url']."</p>";
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
           

     

var_dump($decode2); // let's print it in a more readable format

}
}
?>

</body>
