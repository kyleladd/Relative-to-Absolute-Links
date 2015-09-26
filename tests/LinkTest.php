<?php
// require_once "PHPUnit/Autoload.php";
require_once "Link.php";
 
class LinkTest extends PHPUnit_Framework_TestCase
{
    public function testGetTheAbsoluteDirectory() {
        $this->assertEquals("http://example.com/",getTheAbsoluteDirectory("http://example.com/"));
        $this->assertEquals("http://example.com/",getTheAbsoluteDirectory("http://example.com/index.html"));
        $this->assertEquals("http://example.com/posts/",getTheAbsoluteDirectory("http://example.com/posts/"));
        $this->assertEquals("http://example.com/posts/",getTheAbsoluteDirectory("http://example.com/posts/post.html"));
    }

    public function testIsAbsoluteLink() {
    	$this->assertEquals(true, isAbsoluteLink("http://",0));
    	$this->assertEquals(true, isAbsoluteLink("https://",0));
    	$this->assertEquals(true, isAbsoluteLink("ftp://",0));
    	$this->assertEquals(true, isAbsoluteLink("mailto",0));
    	$this->assertEquals(true, isAbsoluteLink("mailto:someone@example.com",0));
    	$this->assertEquals(true, isAbsoluteLink("http://example.com/",0));
        $this->assertEquals(true, isAbsoluteLink("https://example.com/",0));
    	$this->assertEquals(true, isAbsoluteLink("ftp://example.com",0));
    	$this->assertEquals(true, isAbsoluteLink("#",0));
        $this->assertEquals(false, isAbsoluteLink("index.html",0));
        $this->assertEquals(false, isAbsoluteLink("/home.html",0));
        $this->assertEquals(false, isAbsoluteLink("/posts/post.html",0));
        $this->assertEquals(false, isAbsoluteLink("posts/post.html",0));
        $this->assertEquals(false, isAbsoluteLink("//www.youtube.com",0));
    }

    public function testIsAbsoluteLinkCaseInsensitive() {
        $this->assertEquals(true, isAbsoluteLink("Http://",0));
        $this->assertEquals(true, isAbsoluteLink("Https://",0));
        $this->assertEquals(true, isAbsoluteLink("Ftp://",0));
        $this->assertEquals(true, isAbsoluteLink("MailTo",0));
        $this->assertEquals(true, isAbsoluteLink("MailTo:someone@example.com",0));
        $this->assertEquals(true, isAbsoluteLink("Http://example.com/",0));
        $this->assertEquals(true, isAbsoluteLink("Https://example.com/",0));
        $this->assertEquals(true, isAbsoluteLink("Ftp://example.com",0));
        $this->assertEquals(true, isAbsoluteLink("#",0));
    }

    public function testStartingSlashTrue(){
        $link = "/post/index.php";
        $rootURL = "http://example.com/";
        $theAbsoluteDirectory = "http://example.com/";
        // starting slash is root
        $result = linkStartsWithSlash($link,$rootURL,$theAbsoluteDirectory,0,true,0);
        $this->assertEquals("http://example.com/post/index.php",$result["haystack"]);
        $this->assertEquals(18,$result["adjustment"]);
    }

    public function testStartingSlashTrueURLNotRootDirectory(){
        $link = "/post/index.php";
        $rootURL = "http://example.com/";
        $theAbsoluteDirectory = "http://example.com/posts/";
        $result = linkStartsWithSlash($link,$rootURL,$theAbsoluteDirectory,0,true,0);
        $this->assertEquals("http://example.com/post/index.php",$result["haystack"]);
        $this->assertEquals(18,$result["adjustment"]);
    }

    public function testStartingSlashFalse(){
        $link = "/post/index.php";
        $rootURL = "http://example.com/";
        $theAbsoluteDirectory = "http://example.com/";
        $result = linkStartsWithSlash($link,$rootURL,$theAbsoluteDirectory,0,false,0);
        $this->assertEquals("http://example.com/post/index.php",$result["haystack"]);
        $this->assertEquals(18,$result["adjustment"]);
    }

    public function testStartingSlashFalseURLNotRootDirectory(){
        $link = "/post/index.php";
        $rootURL = "http://example.com/";
        $theAbsoluteDirectory = "http://example.com/posts/";
        $result = linkStartsWithSlash($link,$rootURL,$theAbsoluteDirectory,0,false,0);
        $this->assertEquals("http://example.com/posts/post/index.php",$result["haystack"]);
        $this->assertEquals(24,$result["adjustment"]);
    }

    public function testStartsWithDS(){
        $link = "./post/index.php";
        $theAbsoluteDirectory = "http://example.com/posts/";
        $result = linkStartsWithDS($link,0,$theAbsoluteDirectory,0);
        $this->assertEquals("http://example.com/posts/post/index.php",$result["haystack"]);
        $this->assertEquals(23,$result["adjustment"]);
    }

    public function testStartsWithGETParamsOnRoot(){
        $link = "?test=example";
        $absoluteURL = "http://example.com/posts/";
        $result = prependAbsoluteDirectory($link,0,$absoluteURL,0);
        $this->assertEquals("http://example.com/posts/?test=example",$result["haystack"]);
        $this->assertEquals(25,$result["adjustment"]);
    }

    public function testStartsWithGETParamsNotOnRoot(){
        $link = "?test=example";
        $absoluteURL = "http://example.com/posts/index.php";
        $result = prependAbsoluteDirectory($link,0,$absoluteURL,0);
        $this->assertEquals("http://example.com/posts/index.php?test=example",$result["haystack"]);
        $this->assertEquals(34,$result["adjustment"]);
    }

    public function testPrependAbsoluteDirectory(){
        $link = "myotherpage.html";
        $theAbsoluteDirectory = "http://example.com/posts/";
        $result = prependAbsoluteDirectory($link,0,$theAbsoluteDirectory,0);
        $this->assertEquals("http://example.com/posts/myotherpage.html",$result["haystack"]);
        $this->assertEquals(25,$result["adjustment"]);
    }

    public function testgetCharacterPositions(){
        $absoluteURL = "http://example.com/posts/post/comments/comment/index.php";
        $slashPositions = getCharacterPositions($absoluteURL);
        $this->assertEquals(array(5,6,18,24,29,38,46),$slashPositions);
    }

    public function testLinkStartWithDDS(){
        $link = "../myotherpage.html";
        $slashLastPos = 0;
        $absoluteURL = "http://example.com/a/b/c/d/e/f/mypage.html";
        $slashPositions = getCharacterPositions($absoluteURL);
        $theAbsoluteDirectory = getTheAbsoluteDirectory($absoluteURL);
        $result = linkStartsWithDDS($link,0,$absoluteURL,$slashPositions,0);
        $this->assertEquals("http://example.com/a/b/c/d/e/myotherpage.html",$result["haystack"]);
        $this->assertEquals(26,$result["adjustment"]);
        $link = "../../myotherpage.html";
        $result = linkStartsWithDDS($link,0,$absoluteURL,$slashPositions,0);
        $this->assertEquals("http://example.com/a/b/c/d/myotherpage.html",$result["haystack"]);
        $this->assertEquals(21,$result["adjustment"]);
        $link = "../../../myotherpage.html";
        $result = linkStartsWithDDS($link,0,$absoluteURL,$slashPositions,0);
        $this->assertEquals("http://example.com/a/b/c/myotherpage.html",$result["haystack"]);
        $this->assertEquals(16,$result["adjustment"]);
        $link = "../../../../myotherpage.html";
        $result = linkStartsWithDDS($link,0,$absoluteURL,$slashPositions,0);
        $this->assertEquals("http://example.com/a/b/myotherpage.html",$result["haystack"]);
        $this->assertEquals(11,$result["adjustment"]);
        $link = "../../../../../myotherpage.html";
        $result = linkStartsWithDDS($link,0,$absoluteURL,$slashPositions,0);
        $this->assertEquals("http://example.com/a/myotherpage.html",$result["haystack"]);
        $this->assertEquals(6,$result["adjustment"]);
        $link = "../../../../../../myotherpage.html";
        $result = linkStartsWithDDS($link,0,$absoluteURL,$slashPositions,0);
        $this->assertEquals("http://example.com/myotherpage.html",$result["haystack"]);
        $this->assertEquals(1,$result["adjustment"]);
    }

    public function testLinkStartWithDS(){
        $link = "./myotherpage.html";
        $theAbsoluteDirectory = "http://example.com/posts/";
        $result = linkStartsWithDS($link,0,$theAbsoluteDirectory,0);
        $this->assertEquals("http://example.com/posts/myotherpage.html",$result["haystack"]);
        $this->assertEquals(23,$result["adjustment"]);
    }

    public function testLinkStartWithDSsubdir(){
        $link = "./subdir/myotherpage.html";
        $theAbsoluteDirectory = "http://example.com/posts/";
        $result = linkStartsWithDS($link,0,$theAbsoluteDirectory,0);
        $this->assertEquals("http://example.com/posts/subdir/myotherpage.html",$result["haystack"]);
        $this->assertEquals(23,$result["adjustment"]);
    }

    public function testGetResourcePositions(){
        $haystackString = "This string does contain this <a href='#'>link.</a>";
        $matches = getResourcePositions($haystackString);
        $this->assertEquals(array(array("href='",33)),$matches);
    }

    public function testGetResourcePositionsNone(){
        $haystackString = "This string does not contain a link";
        $matches = getResourcePositions($haystackString);
        $this->assertEquals(array(),$matches);
    }

    public function testGetResourcePositionsCaseUppercase(){
        $haystackString = "This string does contain this <a HREF='#'>link.</a>";
        $matches = getResourcePositions($haystackString);
        $this->assertEquals(array(array("HREF='",33)),$matches);
    }

    public function testGetResourcePositionsCaseInsensitive(){
        $haystackString = "This string does contain this <a HReF='#'>link.</a>";
        $matches = getResourcePositions($haystackString);
        $this->assertEquals(array(array("HReF='",33)),$matches);
    }

    public function testGetResourcePositionsHref(){
        $haystackString = "This string does contain this <a href='#'>link.</a>";
        $matches = getResourcePositions($haystackString);
        $this->assertEquals(array(array("href='",33)),$matches);
    }

    public function testGetResourcePositionsSrc(){
        $haystackString = "This string does contain this <a src='#'>link.</a>";
        $matches = getResourcePositions($haystackString);
        $this->assertEquals(array(array("src='",33)),$matches);
    }

    public function testGetResourcePositionsWildCardSpaceBeforeEqual(){
        $haystackString = "This string does contain this <a href  ='#'>link.</a>";
        $matches = getResourcePositions($haystackString);
        $this->assertEquals(array(array("href  ='",33)),$matches);
    }

    public function testGetResourcePositionsWildCardSpaceAfterEqual(){
        $haystackString = "This string does contain this <a href=  '#'>link.</a>";
        $matches = getResourcePositions($haystackString);
        $this->assertEquals(array(array("href=  '",33)),$matches);
    }

    public function testGetResourcePositionsSingleQuote(){
        $haystackString = "This string does contain this <a href='#'>link.</a>";
        $matches = getResourcePositions($haystackString);
        $this->assertEquals(array(array("href='",33)),$matches);
    }

    public function testGetResourcePositionsDoubleQuote(){
        $haystackString = "This string does contain this <a href=\"#\">link.</a>";
        $matches = getResourcePositions($haystackString);
        $this->assertEquals(array(array("href=\"",33)),$matches);
    }

    public function testGetRootURL(){
        $absoluteURL = "http://example.com/posts/index.php";
        $result = getRootURL($absoluteURL);
        $this->assertEquals("http://example.com/",$result);
    }

    public function testgetCharacterPositionsCaseInsensitiveDefault(){
        $string = "Here Is a String that is case INsensitive";
        $positions = getCharacterPositions($string,"in");
        $this->assertEquals(array(13,30),$positions);
    }

    public function testgetCharacterPositionsCaseInsensitive(){
        $string = "Here Is a String that is case INsensitive";
        $positions = getCharacterPositions($string,"in",true);
        $this->assertEquals(array(13,30),$positions);
    }

    public function testgetCharacterPositionsNoResults(){
        $string = "Here Is a String that is case INsensitive";
        $positions = getCharacterPositions($string);
        $this->assertEquals(array(),$positions);
    }

    public function testgetCharacterPositionsCaseSensitive(){
        $string = "Here Is a String that is not case INsensitive";
        $positions = getCharacterPositions($string,"in",false);
        $this->assertEquals(array("13"),$positions);
    }

    public function testConvertRelativeToAbsolute(){
        $haystackString = "This string does contain this <a href=\"#\">link.</a> Here is another <a href=\"../index.html\">link</a>.";
        $absoluteURL = "http://www.example.com/path/to/directory/index.html";
        $rootURL = "http://www.example.com/";
        $startingSlashMeansRoot = true;
        $result = convertRelativeToAbsolute($haystackString,$absoluteURL,$startingSlashMeansRoot,$rootURL);
        $this->assertEquals("This string does contain this <a href=\"#\">link.</a> Here is another <a href=\"http://www.example.com/path/to/index.html\">link</a>.", $result);
    }
    public function testConvertRelativeToAbsoluteNoRootPassed(){
        $haystackString = "This string does contain this <a href=\"#\">link.</a> Here is another <a href=\"/index.html\">link</a>.";
        $absoluteURL = "http://www.example.com/path/to/directory/index.html";
        $startingSlashMeansRoot = true;
        $result = convertRelativeToAbsolute($haystackString,$absoluteURL,$startingSlashMeansRoot);
        $this->assertEquals("This string does contain this <a href=\"#\">link.</a> Here is another <a href=\"http://www.example.com/index.html\">link</a>.", $result);
    }

    public function testConvertRelativeToAbsoluteRootPassed(){
        $haystackString = "This string does contain this <a href=\"#\">link.</a> Here is another <a href=\"/index.html\">link</a>.";
        $absoluteURL = "http://www.example.com/path/to/directory/index.html";
        $rootURL = "http://www.example.com/root/";
        $startingSlashMeansRoot = true;
        $result = convertRelativeToAbsolute($haystackString,$absoluteURL,$startingSlashMeansRoot,$rootURL);
        $this->assertEquals("This string does contain this <a href=\"#\">link.</a> Here is another <a href=\"http://www.example.com/root/index.html\">link</a>.", $result);
    }

    public function testConvertRelativeToAbsoluteSSIsRoot(){
        $haystackString = "This string does contain this <a href=\"#\">link.</a> Here is another <a href=\"/index.html\">link</a>.";
        $absoluteURL = "http://www.example.com/path/to/directory/index.html";
        $rootURL = "http://www.example.com/root/";
        $startingSlashMeansRoot = true;
        $result = convertRelativeToAbsolute($haystackString,$absoluteURL,$startingSlashMeansRoot,$rootURL);
        $this->assertEquals("This string does contain this <a href=\"#\">link.</a> Here is another <a href=\"http://www.example.com/root/index.html\">link</a>.", $result);
    }
    public function testConvertRelativeToAbsoluteSSIsNotRoot(){
        $haystackString = "This string does contain this <a href=\"#\">link.</a> Here is another <a href=\"/index.html\">link</a>.";
        $absoluteURL = "http://www.example.com/path/to/directory/index.html";
        $startingSlashMeansRoot = false;
        $result = convertRelativeToAbsolute($haystackString,$absoluteURL,$startingSlashMeansRoot);
        $this->assertEquals("This string does contain this <a href=\"#\">link.</a> Here is another <a href=\"http://www.example.com/path/to/directory/index.html\">link</a>.", $result);
    }
    public function testConvertRelativeToAbsoluteTwoStartingSlashesString(){
        $haystackString = "This string does contain this <a href=\"//www.youtube.com/path/to/directory/index.html\">link.</a> Here is another <a href=\"/index.html\">link</a>.";
        $absoluteURL = "http://www.youtube.com/path/to/directory/index.html";
        $startingSlashMeansRoot = false;
        $result = convertRelativeToAbsolute($haystackString,$absoluteURL,$startingSlashMeansRoot);
        $this->assertEquals("This string does contain this <a href=\"http://www.youtube.com/path/to/directory/index.html\">link.</a> Here is another <a href=\"http://www.youtube.com/path/to/directory/index.html\">link</a>.", $result);
    }
    public function testConvertRelativeToAbsoluteTwoStartingSlashes(){
        $haystackString = "<a href=\"//www.youtube.com/path/to/directory/index.html\">link.</a>";
        $absoluteURL = "http://www.youtube.com/path/to/directory/index.html";
        $startingSlashMeansRoot = false;
        $result = convertRelativeToAbsolute($haystackString,$absoluteURL,$startingSlashMeansRoot);
        $this->assertEquals("<a href=\"http://www.youtube.com/path/to/directory/index.html\">link.</a>", $result);
    }
    public function testGetProtocol(){
        $this->assertEquals(getProtocol("http://www.google.com/"),"http://");
        $this->assertEquals(getProtocol("https://www.google.com/"),"https://");
        $this->assertEquals(getProtocol("ftp://www.google.com/"),"ftp://");
    }

}