<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Glimmer</title>
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css" />

<style>
body {font-family: 'Open Sans', sans-serif;font-size:15px;color:rgb(0,0,0);margin:0;padding:0;line-height: 22px; background:#fff;font-weight:300;word-wrap:break-word;}
.header {text-align:center;background:#231f20;padding:10px;}
h1 {text-align:center;font-weight:300;color:rgb(34,44,47);margin-top:20px;}
h1 small {font-size:15px;}
h3 {margin-bottom:5px;color:rgb(34,44,47)}
a {color:#000;}
nav ul {background:#231f20;margin:0;padding:10px 0;text-align:center;}
nav ul li {display:inline-block;}
nav ul li a {color:#fff;text-decoration:none;padding:5px;font-size:15px;}
nav ul li a:hover {color:#ccc;}
li {list-style:none;}
section {padding:0 20px;}
section p {margin-top:0;}
ol li p {margin-left:17px;}


</style>
</head>

<body>

<div class="container">
	<section>
    <div align="center"><img src="<?php echo url("/")."/public/images/logo.png"; ?>" width="100" border="0" /></div>
      <div align="center">
      <?php if($user): ?>
      <table border="0">
      	<tr><td><img src="<?php echo $user->image; ?>" width="300" border="0" /></td></tr>
      	<tr>
      	  <td><strong><?php echo $user->name; ?></strong>, <?php echo $user->age; ?></td>
   	    </tr>
      </table>
      <?php else: ?>
      <p>Invalid user profile</p>
      <?php endif; ?>
      </div></section></div>
</body>
</html>
