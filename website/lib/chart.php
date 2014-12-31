<?
header("Content-type: image/png");

// Copyright (c) Isaac Gouy 2004-2014

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');
require_once(LIB_PATH.'lib_chart.php');

SetChartCacheControl();

$in = WhiteListIn();
$WhiteListTests = WhiteListUnique('test.csv',$in);
$WhiteListLangs = WhiteListUnique('lang.csv',$in);


// DATA ////////////////////////////////////////////////////

list ($Mark,$valid) = ValidMark(TRUE);
list ($Sort,$valid) = ValidSort($valid);
list ($Test,$valid) = ValidTests($WhiteListTests,$valid);
list ($BackText,$valid) = ValidLangs($WhiteListLangs,$valid);

list ($Ratios,$valid) = ValidMatrix('r',1,$valid);
for ($i=0;$i<sizeof($Ratios);$i++) $Ratios[$i] = log10($Ratios[$i]);

// CHART //////////////////////////////////////////////////

$chart = new WideBarChart();
$chart->yAxis(log10axis(axis1000()));

if ($valid){
   $chart->backgroundText($BackText);
   $chart->bars($Ratios);
   $chart->notice($Mark);
   $chart->xAxisLegend('selected '.$Test[0].' programs');

   if ($Sort=='fullcpu'){
      $titletext = $Test[0].' - How many times slower? (CPU secs)';
      $yaxistext = 'program time � fastest program time';
   } elseif ($Sort=='kb'){
      $titletext = $Test[0].' - How many times more memory?';
      $yaxistext = 'program memory-used � least memory-used';
   } elseif ($Sort=='elapsed'){
      $titletext = $Test[0].' - How many times slower? (Elapsed secs)';
      $yaxistext = 'program time � fastest program time';
   } elseif ($Sort=='gz'){ 
      $titletext = $Test[0].' - How many times more code?';
      $yaxistext = 'program code-used � least code-used';
   }
   $chart->yAxisLegend($yaxistext);
   $chart->title($titletext);
}

$chart->frame();
$chart->complete();

?>
