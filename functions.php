<?php

/*********************** Function stopwords ********************************
 * takes a string as input, explodes it into and array($phrase_array) of seperate words loops through
 * through each word comparing it to each word(lowercase) in the file 'Stopwords.txt' if they match
 * PHP function unset is called and deletes that element from array($phrase_array) the remaining words
 * are then implode(d) back into a string seperated by whitespace and returned  ****************/
 
function stopwords($newstring)
        {
		 $stopwords = file('Stopwords.txt');
         $phrase_array = explode(' ',$newstring);
         $i=0;
         $j=0;
         $num_phrases = count($phrase_array);
         $num_stops = count($stopwords);
         for($j=0; $j<$num_phrases; $j++)
         {
         for($i=0; $i<$num_stops;$i++)
         {
		  if(strcasecmp($phrase_array[$j], rtrim($stopwords[$i])) == 0)
		 {
			  unset($phrase_array[$j]);
		 }
	     
	     }
         }
        $result = implode(' ',$phrase_array);
          return $result;
        } 
        
        
        
/******************************** Borda Function ***************************
 * Takes two associative arrays as inputs arguments. The php function array_merge could not 
 * be used to merge the two arrays together, because if there are two duplicate keys in the 
 * arrays a single key is preserved in the resulting  array but the the key is only assigned the 
 * second value(from the second array) for that key.*/
    
function Borda($array1, $array2)
    {
		$keys2 = Array();
        $keys2 = array_keys($array2); // makes an array of number => url in input array. 
        
        $array3 = Array(); // array will now hold the keys of array2 when in loop
        for($i = 0;$i<150;$i++)
        {
            $array3[$keys2[$i]] = $array1[$keys2[$i]] + $array2[$keys2[$i]];
        }
        
         $result = array_filter($array3);
         $result = $array3 + $array1; // Union the two arrays to give one aggregated       
         return $result;        
    }
    


/****************** Function Cosine_sim ***********************
 * takes to associative arrays containing url's and tf-idf values. Each array is looped 
 * through to see if the url's match if they do then the  $dot_product variable adds the
 * product of the tf-idf values to itself each time. Next euc_distA and euc_distB are calculated
 * by adding the squares of each tf-idf value and then getting the square root of the result.
 * Finally Cosine distance is calculated by dividing the Dot Porduct by the product of euc_distA and
 * euc_distB, and the result returned.  */ 
	
	function Cosine_sim($urlA, $urlB) 
	{
		/*******    Dot Product  *******/
        $dot_product = 0;
        foreach($urlA as $WordA => $tf_idfA)
        {
         foreach($urlB as $WordB => $tf_idfB)   
         {
			 if(strcasecmp($WordA, $WordB) == 0) 
			 {
				 $dot_product += $tf_idfA * $tf_idfB;
			 }
		 }   
        }
        
        
        /********  Euclidean Distance  *******/
         $euc_distA = 0;
         $euc_distB = 0;
         $euc_A = 0;
         $euc_B = 0;
         
        foreach($urlA as $WordA => $tf_idfA)
        {
	        $euc_A += pow($tf_idfA, 2);
		}
          $euc_distA = sqrt($euc_A); 
            
            
        foreach($urlB as $WordB => $tf_idfB)
        {
			$euc_B += pow($tf_idfB, 2);
		}
		  $euc_distB = sqrt($euc_B); 
             
             
             /***** Calc Cosine difference *****/
             $cosine = 0;
             $cosine = $dot_product/($euc_distA * $euc_distB);
            
             return $cosine;
    } 
    

/*************************** Recalc centroid *******************
 * Takes an individual cluster as it's input argument. Takes each 'member' and 
 * adds the tf-idf values together for each word and then divides that value by the number
 * of members. Return one associative array that has term => value which represents a feature vector
 * which when the function is called is the new centroid. *******/
function RecalcCentroid($clust)
{
		$num_members = count($clust['members']);
        $buffer_arr = $clust['members'];
	    $m =0;
	    $temp = array();
	    
	    /******** each  $buffer_arr only has one element => url => values ********/     
	    
	    for($m=0;$m<$num_members; $m++)
	    {
			foreach($buffer_arr[$m] as $url)  
	        {
			  $temp[$m] = $url;
		    }
	    }
	      
	      $t =0;
	      $temp2 = array();
	      $temp2[0] = $temp[0]; // need to call Borda to add values when words match.
	      for($t=0; $t<$num_members-1; $t++)
	      {
			$temp2[$t+1] = Borda($temp2[$t],$temp[$t+1]); // add values together
		  }

	      arsort($temp2[$num_members-1]);  // sort array
	      array_pop($temp2[$num_members-1]); // 'pop' last NULL element
	      
	      $tempor = Array(); // new array to be returned 
	      foreach($temp2[$num_members-1] as $key => $value)
	      {
			  $tempor[$key] = $value/$num_members; // mean values
		  } 
	      /* returns an array with string => value, representing new centroid(a feature vector) */
	    return $tempor; 
}

?>
