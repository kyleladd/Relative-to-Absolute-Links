<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<style>
  iframe{
    border:none;
  }
  body{
    margin:0;
  }
  div.alert{
    position:fixed;
    top:0;
    left:0;
    right:0;
  }
</style>
<script>
$(document).ready(function(){
  $('#ifr').load(function(){
    $('#ifr').contents().find('#content .rightcontent').html("<div>Injected Content</div>");
  });
});
</script>
</head>
<body>
  <iframe id="ifr" src="../proxy.php?url=http://www.morrisville.edu/student_life/&format=html" width="100%" height="100%"></iframe>
</body>
</html>