<?php
    /*** Function takes a string as it's input argument. Punctuation is first removed  */ 
    function preprocessing($strings)
    {
     $string = $strings;
    
     
     /****** Remove punctuation *******/
     $delimiters = Array(".","!"," ","?",";",":");
     function Punctuation($delimiters,$string)
     {
    $return_array = Array($string); // The array to return
    $i = 0;
    while (isset($delimiters[$i])) // Loop to loop through all delimiters
    {
        $new_return_array = Array(); 
        foreach($return_array as $el_to_split) // Explode all returned elements by the next delimiter
        {
            $put_in_new_return_array = explode($delimiters[$i],$el_to_split);
            foreach($put_in_new_return_array as $substr) // Put all the exploded elements in array to return
            {
                $new_return_array[] = $substr;
            }
        }
        $return_array = $new_return_array; // Replace the previous return array by the next version
        $i++;
    }
    return $return_array; // Return the exploded elements
    }
 
  $text = Punctuation($delimiters, $string);
 
  
 
/***********************  stopwords ********************************
 * takes a string as input, explodes it into and array($phrase_array) of seperate words loops through
 * through each word comparing it to each word(lowercase) in the file 'Stopwords.txt' if they match
 * PHP function unset is called and deletes that element from array($phrase_array) the remaining words
 * are then implode(d) back into a string seperated by whitespace and returned  ****************/
  $newstring = implode( " ", $text);
  
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
  
  /*** An implementation of the Porter Stemming algorithm, however it does not keep
   * track of how many consonants preceed the ens of the word that is currently being removed
   * Example: the word 'bring' has two consonants before the ing a true stemming algorithm would allow
   * for this. My implementation does not but I think with enough time I could rewrite it to do so**/
  function stemmer($word)
    {
		 /* remove plurals and ed or ing */
		 
		if ( substr($word, -1) == 's' ) 
		{
            if ( substr($word, -4) == 'sses' ) 
            {
                $word = substr($word, 0, -2); // Trims last two from $word
            } 
            elseif ( substr($word, -3) == 'ies' )  // Trims last three from $word
            {
                $word = substr($word, 0, -2);
            } 
            elseif ( substr($word, -2, 1) != 's' ) 
            {
                // If second-to-last character is not "s"
                $word = substr($word, 0, -1);
            }
		}
		
		if ( substr($word, -2) == 'ed' ) 
		{
          $word = substr($word, 0, -2);
        } 
        if (substr($word, -3) == 'ing')
        {
			$word = substr($word, 0, -3);
		}	    	
			    	
         
        if ( substr($word, -2) == 'at' || substr($word, -2) == 'bl' ||
                     substr($word, -2) == 'iz' ) 
                    {
                      $word .= 'e';  // .= is concatenating assignment operator.
			    	}
		
		if (substr($word, -6) == 'tional')
		{
			$word = substr($word, 0,-2);
		}
		
		if (substr($word, -4) == 'izer')
		{
			$word = substr($word, 0, -1);
		}
		
		if (substr($word, -7) == 'ization')
		{
			$word = substr($word, 0, -5);
			$word .= 'e';
		}
		
		if (substr($word, -7) == 'isation')
		{
			$word = substr($word, 0, -7);
			$word .= 'ize';
		}
			    	
		if (substr($word, -3) == 'ize')
		{
			$word = substr($word, 0, -3);
		}
		
		if (substr($word, -5) == 'ation')
		{
			$word = substr($word, 0, -3);
			$word .= 'e';
		}
		
		if (substr($word, -5) == 'alism')
		{
			$word = substr($word, 0, -3);
		}
		
		if (substr($word, -7) == 'iveness')
		{
			$word = substr($word, 0, -4);
		}
		
		if (substr($word, -7) == 'fulness')
		{
			$word = substr($word, 0, -4);
		}
		
		if (substr($word, -7) == 'ousness')
		{
			$word = substr($word, 0, -4);
		}
		
		if (substr($word, -5) == 'aliti')
		{
			$word = substr($word, 0, -3);
		}
		
		   if (substr($word, -6) == 'biliti')
		{
			$word = substr($word, 0, -5);
			$word .= 'le';
		}
		
		   if (substr($word, -5) == 'iviti')
		{
			$word = substr($word, 0, -3);
			$word .= 'e';
		}
		
		   if (substr($word, -5) == 'ative')
		{
			$word = substr($word, 0, -5);
		}
		
		   if (substr($word, -5) == 'iviti')
		{
			$word = substr($word, 0, -3);
			$word .= 'e';
		}
		
		  if (substr($word, -5) == 'alize')
		{
			$word = substr($word, 0, -3);
		}
		
		  if (substr($word, -5) == 'icate')
		{
			$word = substr($word, 0, -3);
		}
		
		  if (substr($word, -4) == 'ical')
		{
		    $word = substr($word, 0, -2);
		}
		
		  if (substr($word, -3) == 'ful')
		{
		    $word = substr($word, 0, -3);
		}
		
		  if (substr($word, -2) == 'al')
		{
		    $word = substr($word, 0, -2);
		}
		
		  if (substr($word, -4) == 'ness')
		{
		    $word = substr($word, 0, -4);
		}
		
		  if (substr($word, -4) == 'ance')
		{
		    $word = substr($word, 0, -4);
		}
		  if (substr($word, -4) == 'ence')
		{
		    $word = substr($word, 0, -4);
		}
		
		  if (substr($word, -2) == 'er')
		{
		    $word = substr($word, 0, -2);
		}
		
		  if (substr($word, -3) == 'ism')
		{
		    $word = substr($word, 0, -3);
		}
		
		  if (substr($word, -3) == 'ous')
		{
		    $word = substr($word, 0, -3);
		}
		
		  if (substr($word, -3) == 'ive')
		{
		    $word = substr($word, 0, -3);
		}
		
		  if (substr($word, -3) == 'ize')
		{
		    $word = substr($word, 0, -3);
		}
		
		return $word;
    } // End of function stemmer
     
            
       
     $query_array = explode(" ", $result);
     $j = count($query_array);
     $i=0;
     for($i=0; $i<$j; $i++)
     {
	    $query_arr[$i] = stemmer($query_array[$i]);	
	    
	 }
	
     $phrase2 = implode(" ", $query_arr);
     
     return $phrase2; // return of function preprocessing
    } // End of function preprocessing
?>
