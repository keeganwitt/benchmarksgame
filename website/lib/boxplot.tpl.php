<?   // Copyright (c) Isaac Gouy 2009-2014 ?>

<?
   MkMenuForm($Tests,NULL,$Langs,NULL);

   list($labels,$stats) = $Data;
   unset($Data);  

   // make 2 charts -- middle data & first+last data

   $n = sizeof($stats); 
   $labels1 = array();
   $stats1 = array();

   $m = min($n,21);
   for ($i=6; $i<$m; $i++){
      $labels1[] = $labels[$i];
      $stats1[] = $stats[$i];
   }

   for ($i=0; $i<9; $i++){
      $labels2[] = $labels[$i];
      $stats2[] = $stats[$i];
   }
   $m = $n - 6;
   for ($i=$m; $i<$n; $i++){
      $labels2[] = $labels[$i];
      $stats2[] = $stats[$i];
   }
   unset($labels); 
   unset($stats); 

   $pageUrl = CORE_SITE.SITE_NAME.'/which-programs-are-fastest.php';
   $chart = 'chartbox.php';
?>

<p><em>"What gets us into trouble is not what we don't know, it's what we know for sure that just ain't so."</em></p>

<h2><a href="<?=$pageUrl;?>#chart" name="chart">&nbsp;<strong>Which programs are fastest?</strong></a></h2>

<p>Please don't use this summary page to compare 2 programming language implementations -- <b>use the direct comparison</b>, for example <a href="jruby.php" title="Are the JRuby programs faster than the Ruby programs?">-all&nbsp;benchmarks- Ruby&nbsp;JRuby</a>.</p>


<p>This box plot shows <em>how many times slower</em>, the fastest benchmark programs for selected programming language implementations were, compared to the fastest programs written in <em>any of the programming languages</em>.</p>

<p>Note which boxes have no overlap, note which boxes <b>overlap completely</b>; note which show little midspread, note which spread across too large a range of values for confidence.</p>


<p><img src="<?=$chart;?>?<?='s='.Encode($stats1);?>&amp;<?='m='.Encode($Mark);?>&amp;<?='w='.Encode($labels1);?>"
   alt=""
   title=""
   width="480" height="300"
 /></p>

<p>These are not the only compilers and interpreters. These are not the only programs that could be written. These are not the only tasks that could be solved. <a href="<?=CORE_SITE;?>dont-jump-to-conclusions.html"><strong>These are just 10 tiny examples.</strong></a></p>
<p>Please don't obsess over tiny differences in median values from such a small number of examples.</p>

<p><img src="<?=$chart;?>?<?='s='.Encode($stats2);?>&amp;<?='m='.Encode($Mark);?>&amp;<?='w='.Encode($labels2);?>"
   alt=""
   title=""
   width="480" height="300"
 /></p>

<p>Please don't obsess about which programming language implementation is shown 10<sup>th</sup> and which is shown 11<sup>th</sup>. You can see that the order would be different if it was based on the median scores instead of <a href="http://portal.acm.org/citation.cfm?id=5666.5673" title="How not to lie with statistics: the correct way to summarize benchmark results">the geometric mean</a> scores.</p>



<h3><a href="<?=$pageUrl;?>#about" name="about">&nbsp;</a></h3>
<?=$About;?>


