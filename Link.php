<?php
/** 
* convertRelativeToAbsolute 
* 
* Replace all occurrences of relative links within string with absolute links 
* 
* @param string $haystackString 
* @param string $absoluteURL
* @param boolean $startSlashMeansRoot
* @param string $rootURL 
* @return string $haystackString
*/
	function convertRelativeToAbsolute($haystackString,$absoluteURL,$startingSlashMeansRoot,$rootURL=""){
  $adjustment = 0;
  $theAbsoluteDirectory = getTheAbsoluteDirectory($absoluteURL);
  $slashPositions = getCharacterPositions($absoluteURL);
  $matches = getResourcePositions($haystackString);
  if($rootURL==""){
    $rootURL = getRootURL($absoluteURL);
  }
  if(isset($matches)){//if positions meaning if there are relative links within the string provided
    foreach ($matches as $value) {
      $start=intval($value[1])+intval(strlen($value[0]))+intval($adjustment);
      if(isAbsoluteLink($haystackString,$start)!==true){
        //it is a relative link and changes are required
        //If starting slash means go to rootURL
        if(substr($haystackString, $start,1)=="/"){ 
          $result = linkStartsWithSlash($haystackString,$rootURL,$theAbsoluteDirectory,$start,$startingSlashMeansRoot,$adjustment);
          $haystackString = $result['haystack'];
          $adjustment = $result['adjustment'];
        }
        elseif(substr($haystackString, $start,3)=="../"){
          $result = linkStartsWithDDS($haystackString,$start,$absoluteURL,$slashPositions,$adjustment);
          $haystackString = $result['haystack'];
          $adjustment = $result['adjustment'];
        }
        //prepend the absolute url to the get url
        elseif(substr($haystackString,$start,1)=="?"){
          $result = prependAbsoluteDirectory($haystackString,$start,$absoluteURL,$adjustment);
          $haystackString = $result['haystack'];
          $adjustment = $result['adjustment'];
        }
        elseif(substr($haystackString,$start,2)=="./"){
          $result = linkStartsWithDS($haystackString,$start,$theAbsoluteDirectory,$adjustment);
          $haystackString = $result['haystack'];
          $adjustment = $result['adjustment'];
        }
        else{
          $result = prependAbsoluteDirectory($haystackString,$start,$theAbsoluteDirectory,$adjustment);
          $haystackString = $result['haystack'];
          $adjustment = $result['adjustment'];
        }
      }
    }
  }//end of if positions meaning if there are relative links within the string provided
  return $haystackString;
}

/** 
* getCharacterPositions 
* 
* Get all positions of character(s) within string.  Can be either case sensitive or insensitive.
* 
* @param string $haystack 
* @param string $char
* @param boolean $stri
* @return array $positions
*/
function getCharacterPositions($haystack,$char = "/",$stri = true){
  $lastPos = 0;
  $positions=array();
  if ($stri) { 
      $haystack = strtolower($haystack); 
      $char = strtolower($char); 
    } 
  while (($lastPos = strpos($haystack, $char, $lastPos))!==false) {
    $positions[] = $lastPos;
    $lastPos = intval($lastPos) + (strlen($char));
  }
  return $positions;
}

/** 
* isAbsoluteLink 
* 
* Determine if a link at position in the string is relative or absolute based on protocol.
* 
* @param string $haystackString 
* @param int $start
* @return boolean
*/
function isAbsoluteLink($haystackString,$start){
  $protocols = array("http://","https://","ftp://","mailto","#");
  foreach ($protocols as $protocol){
    if(strtolower(substr($haystackString, $start, strlen($protocol)))==$protocol){
      return true;
    }
  }
  return false;
}

/** 
* linkStartsWithSlash 
* 
* When a link starts with slash, on a Linux system this typically means go to root while Windows is the current directory.
* Convert relative link to the appropriate absolute link.
* 
* @param string $haystackString 
* @param string $absoluteURL
* @param boolean $startSlashMeansRoot
* @param string $rootURL 
* @return string $haystackString
*/
function linkStartsWithSlash($haystackString,$rootURL,$theAbsoluteDirectory,$start,$startingSlashMeansRoot,$adjustment){
  if($startingSlashMeansRoot==true){
    //starts with a slash, remove the slash and put the root url
    $haystackString = substr_replace($haystackString, $rootURL, $start, 1);
    $adjustment = (intval(strlen($rootURL))-1)+intval($adjustment);
  }
  else{
    //starting slash means current directory
    $haystackString = substr_replace($haystackString, $theAbsoluteDirectory, $start, 1);
    $adjustment = (intval(strlen($theAbsoluteDirectory))-1)+intval($adjustment);
  }
  return array('haystack'=>$haystackString,'adjustment'=>$adjustment);
}

/** 
* linkStartsWithDDS 
* 
* Replace "../" (up a directory) in string with the appropriate absolute url when provided the current url.
* 
* @param string $haystackString 
* @param int $start
* @param string $absoluteURL
* @param array $slashPositions
* @param int $adjustment 
* @return array(string,int)
*/
function linkStartsWithDDS($haystackString,$start,$absoluteURL,$slashPositions,$adjustment){
  $ddsPos = $start;
  $numddsToReplace = 0;
  $ddsr = true;
  //look for all ../ after the current position in a row
  while($ddsr==true){
    if(substr($haystackString,$ddsPos,3)=="../"){
      $numddsToReplace++;
      $ddsPos = intval($ddsPos)+3;
    }
    else{
      $ddsr = false;
    }
  }
  //Replace the all the leading ../ with the corresponding portion of the current directory
  $haystackString = substr_replace($haystackString, substr($absoluteURL,0,intval($slashPositions[count($slashPositions)-(intval($numddsToReplace))-1]+1)), $start, 3*intval($numddsToReplace));
  $adjustment = intval($slashPositions[count($slashPositions)-(intval($numddsToReplace))-1]+1)-(intval($numddsToReplace)*3)+intval($adjustment);
  return array('haystack'=>$haystackString,'adjustment'=>$adjustment);
}

/** 
* linkStartsWithDS 
* 
* Insert the absolute directory at position in string in place of "./". 
* 
* @param string $haystackString 
* @param int $start
* @param string $theAbsoluteDirectory
* @param int $adjustment 
* @return array(string,int)
*/
function linkStartsWithDS($haystackString,$start,$theAbsoluteDirectory,$adjustment){
  $haystackString = substr_replace($haystackString, $theAbsoluteDirectory, $start, 2);
  $adjustment = intval(strlen($theAbsoluteDirectory)-2)+intval($adjustment);
   return array('haystack'=>$haystackString,'adjustment'=>$adjustment);
}

/** 
* prependAbsoluteDirectory 
* 
* Insert the absolute directory at position in string. 
* 
* @param string $haystackString 
* @param int $start
* @param string $theAbsoluteDirectory
* @param int $adjustment 
* @return array(string,int)
*/
function prependAbsoluteDirectory($haystackString,$start,$theAbsoluteDirectory,$adjustment){
  $haystackString = substr_replace($haystackString, $theAbsoluteDirectory, $start, 0);
  $adjustment = intval(strlen($theAbsoluteDirectory))+intval($adjustment);
  return array('haystack'=>$haystackString,'adjustment'=>$adjustment);
}

/** 
* getTheAbsoluteDirectory 
* 
* Get the current directory from the url provided.
* 
* @param string $theAbsoluteURL 
* @return string $theAbsoluteDirectory
*/
function getTheAbsoluteDirectory($theAbsoluteURL){
  $slashLastPos=0;
  while (($slashLastPos = strpos($theAbsoluteURL, "/", $slashLastPos))!== false) {
      $slashPositions[] = $slashLastPos;
      $slashLastPos = intval($slashLastPos) + 1;
  }
  //Absolute Directory: beginning of the string to the Last slash (+1 includes the slash in the string)
  $theAbsoluteDirectory=substr($theAbsoluteURL, 0, end($slashPositions)+1);
  return $theAbsoluteDirectory;
}

/** 
* getResourcePositions 
* 
* Find the positions of all links to resources within string. 
* 
* @param string $haystackString 
* @return array $matches[0]
*/
function getResourcePositions($haystackString){
  $pattern = "/(src|href)\s*(=)\s*('|\")/i";
  preg_match_all($pattern, $haystackString, $matches, PREG_OFFSET_CAPTURE);
  return $matches [0];
}

/** 
* getRootURL 
* 
* Return the root url from the url given. 
* 
* @param string $theAbsoluteURL 
* @return string 
*/
function getRootURL($theAbsoluteURL){
  $slashpositions = getCharacterPositions($theAbsoluteURL);
  return substr($theAbsoluteURL, 0, $slashpositions[2] + 1); 
}