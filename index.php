<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<!-- Sentinel.php is main interface page that also contains a link to the advanced 
     search page(Advanced_Search.php). It runs off a number of radio type buttons that
     allows the user to view results in a number of different ways: each engine, seperate lists 
     or an aggregated list. The results may be clustered or displayed as ranked lists. The manner in
     which the submitted query can be altered is choosen by either 'Query Rewrite' or 'Query preprocessing'
     I choose a very simple display that is styled using one external style sheet 'sentinel.css' an
     explanation of the display of the clustered results is included in the comments on each cluster PHP
     file. An explanation of the Advanced Search page is included at the start of that page. Note the boolean 
     search is not included in the main page and should be done by clicking on the link 'Advanced Search'   

-->
<head>
	<link rel="stylesheet" type="text/css" href="sentinel.css" />
	<div id="top">
      <h1>Sentinel Search</h1>
        <form method="get" action="Sentinel.php">
           <input type="text" name="search" size="50" />
           <input type="submit" name="submit" value="Submit" />
           <a href='Advanced_Search.php'>Advanced Search</a>
           <br>
            <tr>
		     <td align="right" valign="middle">Display results from:</td>
             <input type="radio" name="engine" value="yahoo">
             EntireWeb
             <input type="radio" name="engine" value="bing">
             Bing
             <input type="radio" name="engine" value="blekko">
             Blekko
             <input type="radio" name="engine" value="individual">
             Seperate lists
             <input type="radio" name="engine" checked value="aggregated">
             Aggregated results
             </td>
            </tr>
          <br>
          <tr>
          <td align="let" valign="left">Query Preprocessing:</td>
            <input type="radio" name="preprocessing" value="On">
            On
            <input type="radio" name="preprocessing" checked value="Off">
            Off    
          </td><br>
         <td align="right" valign="right">Query Rewrite:</td>
            <input type="radio" name="rewrite" value="On">
            On
            <input type="radio" name="rewrite" checked value="Off">
            Off
          </td>
          </tr><br>
          <td align="right" valign="right">Cluster Results:</td>
            <input type="radio" name="cluster" value="On">
            On
            <input type="radio" name="cluster" checked value="Off">
            Off
          </td>
          </tr>
        </form>
    </div>
    </head>
 <body>   
<?php 
     include_once('preprocessing.php');
     include_once('API_Module.php');
     include_once('query_rewrite.php');
     
    $search = trim($_GET['search']); // trim whitespace at start and end to ensure no empty query is submitted
    if($_GET['submit']) 
    {
        if(strlen($search) == 0) // error handling for empty search
        {
          echo "<p>Error: empty search</p>";
        }
    else 
    {
        switch($_GET['preprocessing'])
        {
          case 'On':
           $search_api = preprocessing($search); // calls preprocessing else query remains as input
           break;
          case 'Off':
           $search_api = $search;
           break;
	    }
         
         echo "You searched for: ";
         print_r($search_api);
         echo "<br>";
		 echo "<p><a href='Sentinel.php'>Home</a></p>";
		
		switch($_GET['rewrite'])
		{
			case 'Off': // calls API_Module with $search_api(query) as arguement 
		    $returned_results = API_Module($search_api); 
		    break;
		    case 'On': // calls Query_Rewrite with $search_api(query) as arguement
		    $returned_results = Query_Rewrite($search_api);
		    break;
		}
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
		else  // if not clustered display normal page results.
        { 
        switch($_GET['engine'])  // displays the following
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
          case 'aggregated':
           include_once('aggregated.php');
           break;   
        }  // End switch   
	    }
    }  // End else
	}  // End if
    
   
?>

</body>
</html>
