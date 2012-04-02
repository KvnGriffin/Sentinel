<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- Advanced Search facility works be naming each search box and then using a number of 
     different if statements to catch all scenario's of user input. The string is then modified 
     to reflect the users choice before being sent to API_Module i.e a - or an OR is added in front of 
     each AND is assumed by all search engine API's so it did not need to be included.
     
     ***** NOTE: The Blekko API cannot handle many multiple terms in the string that is sent to it 
     ***** so the aggregated results may be for only Bing and Yahoo, depending on how complex the 
     ***** search is. I have also removed query preprocessing and query rewrite from the Advanced Search.
    
-->
<html>
<head>
	<link rel="stylesheet" type="text/css" href="sentinel.css" />

	<div id="top">
      <h1>Sentinel Search</h1>
          <form method="get" action="Advanced_Search.php">
           Search for:<input type="text" name="search1" size="48" />
           <input type="submit" name="submit" value="Submit" />
           <br>
         
          <p>One or more of these words: <input type="text" name="search2" size="20" /><b>OR</b>
           <input type="text" name="search3" size="20" /><b>OR</b>
           <input type="text" name="search4" size="20" /><p/>
         <p>Don't show pages that include:<input type="text" name="search5" size="73" /><p/>
         <tr>
         <td align="right" valign="middle">Display results from:</td>
           <td>
            <input type="radio" name="engine" value="yahoo">
            EntireWeb
            <input type="radio" name="engine" value="bing">
            Bing
            <input type="radio" name="engine" value="blekko">
            Blekko
             <input type="radio" name="engine" value="individual">
            Seperate lists
            <input type="radio" name="engine" checked value="rank">
            Ranked aggregate results
           </td>
           </tr>
           <br>
          <td align="right" valign="right">Cluster Results:</td>
            <input type="radio" name="cluster" value="On">
            On
            <input type="radio" name="cluster" checked value="Off">
            Off
          </td>
          </form>
    <div/>  
    <a href='Sentinel.php'>Home</a>
 </head>
 
<body>   
<?php 
    
    include_once('API_Module.php');
    
    $search1 = trim($_GET['search1']); // trim whitespace at start and end to ensure no empty query is submitted
    $search2 = trim($_GET['search2']);
    $search3 = trim($_GET['search3']);
    $search4 = trim($_GET['search4']);
    $search5 = trim($_GET['search5']);
    
    
    if($_GET['submit']) 
    {
        if(strlen($search1) == 0) 
        {
         echo "<p>Error: empty search</p>";
        }
        else
        {
		 
		 /******************* Catch all scenario's of user input *************/
		 if(strlen($search2) == 0 && strlen($search3) == 0 && strlen($search4) == 0)
         {
          $search_api = $search1;   
         }
         elseif(strlen($search2) != 0 && strlen($search3) == 0 && strlen($search4) == 0)
         {
			$search_api = "".$search1." ".$search2."";
		 }
		 elseif(strlen($search2) == 0 && strlen($search3) != 0 && strlen($search4) == 0)
         {
			$search_api = "".$search1." ".$search3."";
		 }
		 elseif(strlen($search2) == 0 && strlen($search3) == 0 && strlen($search4) != 0)
         {
			$search_api = "".$search1." ".$search4."";
		 }
		
		/*************** All the 'OR' scenario's ****************************/
		 if(strlen($search2) != 0 && strlen($search3) != 0 && strlen($search4) == 0)
         {
			$search_api = "".$search1." ".$search2." OR ".$search3."";
		 }
		 elseif(strlen($search2) == 0 && strlen($search3) != 0 && strlen($search4) != 0)
         {
			$search_api = "".$search1." ".$search3." OR ".$search4."";
		 } 
		 elseif(strlen($search2) != 0 && strlen($search3) != 0 && strlen($search4) != 0)
         {
			$search_api = "".$search1." ".$search2." OR ".$search3." OR ".$search4."";
		 }
		
		
		/*************** All the NOT scenario's  ********************************/
		
		    if(strlen($search5) != 0)
            {
			  $minus ="-";
			  $search5_array = explode(" ",$search5);
			  $b=0;
			  $arr_count = count($search5_array);
			
			    for($b=0; $b<$arr_count; $b++ )
			    {
			       $search5_array[$b] = $minus.$search5_array[$b];
			    }
		     
		       $search5_arrayimp = implode(" ",$search5_array);
		       $search_api = "".$search_api." ".$search5_arrayimp."";
		    }
	        
	        echo "You searched for: ";
	        echo $search_api;
		    echo "<br>";
		    
	        
	        $returned_results = API_Module($search_api); 
	        
	        /********** Results to be displayed ************************/
	      echo "<div style='text-align: left'>";  // align results to the left.   
	        /*****************   calls each clustering page  *************************/
        if($_GET['cluster'] == 'On' && $_GET['engine'] == 'bing')
        { 
			include_once('bing_cluster.php');
		}
		elseif($_GET['cluster'] == 'On' && $_GET['engine'] == 'yahoo')
        { 
			include_once('yahoo_cluster.php');
		}
		elseif($_GET['cluster'] == 'On' && $_GET['engine'] == 'aggregated')
        { 
			include_once('aggregated_cluster.php');
		}
		elseif($_GET['cluster'] == 'On' && $_GET['engine'] == 'individual')
        { 
			include_once('individual_cluster.php');
		}
		elseif($_GET['cluster'] == 'On' && $_GET['engine'] == 'blekko')
        { 
			include_once('blekko_cluster.php');
		}
		else 
		{ // if not clustered display normal page results.
	       
            switch($_GET['engine'])
            {
             case 'yahoo':
               include_once('yahoo_results.php');
               break;
             case 'bing':
               include_once('bing_results.php');
               break;
             case 'blekko':
               include_once('blekko_results.php');
               break;
             case 'individual':
               include_once('all_results.php');
               break;
             case 'rank':
               include_once('aggregated.php');
               break;   
            } // End switch 
            echo "</div>";
	     } // end else   
	    } // End else 
    } // End if
       
   
?>

</body>
</html>
 
 

