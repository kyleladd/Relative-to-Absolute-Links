# Relative-to-Absolute-Links
Convert all relative links within string to absolute

[![Build Status](https://travis-ci.org/kyleladd/Relative-to-Absolute-Links.svg)](https://travis-ci.org/kyleladd/Relative-to-Absolute-Links)


## Install Dependencies
```
composer install
```

## Run tests
```
vendor/phpunit/phpunit/phpunit tests
```

## Options

### url
  * Url to get the document of as a string and convert all of its resources from relative to absolute.
  * Default: 
```html
<iframe id="ifr" src="../proxy.php?url=http://www.example.com" width="100%" height="100%"></iframe>
```  

### rooturl
  * Controls the vertical positioning of the marker relative to the marker location. The change only affects markers subsequently inserted
  * Valid Values: {url string}
  * Default: "" - fetches the Document Root
```html
<iframe id="ifr" src="../proxy.php?url=http://www.example.com&rooturl=http://www.example.com/" width="100%" height="100%"></iframe>
``` 

### slashIsRoot
  * When a link starts with slash, on a Linux system this typically means go to root while Windows is the current directory. Convert relative link to the appropriate absolute link.
  * Valid Values: true, false
  * Default: true 
```html
<iframe id="ifr" src="../proxy.php?url=http://www.morrisville.edu/academics/&slashisroot=true" width="100%" height="100%"></iframe>
```

### format
  * Output as JSON for $.getJSON() or as html for an iframe .
  * Valid Values: json, iframe
  * Default: json 
```html
<iframe id="ifr" src="../proxy.php?url=http://www.morrisville.edu/academics/&format=html" width="100%" height="100%"></iframe>
<script>
$(document).ready(function(){
  $('.alert').alert();
  $('#ifr').load(function(){
    $('#ifr').contents().find('#content .rightcontent').html("<h1>Example preview changes.</h1>");
  });
});
</script>
```  