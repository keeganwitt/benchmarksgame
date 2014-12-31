<?php

// REVISED - don't have all pages expire at the same time!
// EXPIRE pages 10 days after they are visited.
$s = time();
header("Pragma: public");
header("Cache-Control: maxage=".(240*3600).",public");
header("Expires: " . gmdate("D, d M Y H:i:s", $s + (240*3600)) . " GMT");
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />

<?=$Robots;?>
<?=$MetaKeywords;?>

<title><?=$PageTitle;?></title>
<link rel="stylesheet" type="text/css" href="http://benchmarksgame.alioth.debian.org/benchmark_css_8oct2012.php" />
<link rel="shortcut icon" href="http://benchmarksgame.alioth.debian.org/favicon_ico_11dec2009.php" />
<?
   if (isset($LinkCanonical)) { echo $LinkCanonical; }
?>
<meta name="viewport" content="width=536,user-scalable=yes"/>
</head>
<body id="<?=SITE_NAME;?>">

<?
   if ($PageId == 'faq') { 
      $FaqRollover = " Apples and Oranges. Programmer skill and effort. A good starting point."; 
   } else { 
      $FaqRollover = "How programs were measured. FAQs. How to contribute programs."; 
   }
?>

<table class="banner"><tr>
<td><h1><a href="<?=$BannerUrl;?>" title="quad-core and one core, x86 and x64 measurements"><?=$BannerTitle;?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$FaqUrl;?>" title="<?=$FaqRollover;?>"><?=$FaqTitle;?></a></h1></td>
</tr></table>

<div id="<?=$PageId;?>">
<?=$PageBody;?>

<p class="imgfooter">
<a href="<?=CORE_SITE;?>license.html" title="The Computer Language Benchmarks Game is published under this revised BSD license" >
   <img src="<?=IMAGE_PATH;?>open_source_button_png_11dec2009.php" alt="Revised BSD license" height="31" width="88" />
</a>
</p>
<p class="imgfooter">&nbsp; <a href="<?=CORE_SITE;?>">Home</a> &nbsp; <a href="<?=CORE_SITE;?>dont-jump-to-conclusions.html">Conclusions</a> &nbsp; <a href="<?=CORE_SITE;?>license.html">License</a> &nbsp; <a href="<?=CORE_SITE;?>play.html">Play</a> &nbsp;</p>
</div>


<script type="text/javascript">var _gaq=_gaq||[];_gaq.push(['_setAccount','UA-37137205-1']);_gaq.push(['_trackPageview']);(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s);})();</script>
</body>
</html>
