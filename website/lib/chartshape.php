<?
header("Content-type: image/png");

// Copyright (c) Isaac Gouy 2009-2011

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');
require_once(LIB_PATH.'lib_chart.php');

SetChartCacheControl();

$in = WhiteListIn();
$WhiteListLangs = WhiteListUnique('lang.csv',$in);


// DATA ////////////////////////////////////////////////////

list ($LangName,$valid) = ValidLangs($WhiteListLangs,TRUE);

list ($Shapes,$valid) = ValidMatrix('s',1,$valid);
for ($i=0;$i<sizeof($Shapes);$i++) $Shapes[$i] = log10($Shapes[$i]);

list ($Centers,$valid) = ValidMatrix('c',1,$valid);
for ($i=0;$i<sizeof($Centers);$i++) $Centers[$i] = log10($Centers[$i]);


// CHART //////////////////////////////////////////////////

   $w = 150;
   $h = 120;

   $xo = 48;
   $yo = MARGIN;


$chart = new ShapeChart();

if ($valid){
   $chart->yAxis(log10axis(axis3_5_10()));
   $chart->xAxis(log10axis(axisOneTen()),101.0);
   $chart->frame();

   $chart->title_($LangName[0],2,CHAR_WIDTH_2);
   // hard-code a shortest programs example
   if (!isset($LangName[0])){ $chart->title_('Shortest C++',2,CHAR_WIDTH_2); }

   $chart->shapes($Shapes,$Centers);

   $label = 'concise';
   ImageString($chart->im, 2, $xo, $h-$yo+4, $label, $chart->colour[DARK_GRAY]);
   $label = 'fast';
   ImageStringUp($chart->im, 2, 30, $h, $label, $chart->colour[DARK_GRAY]);
}
$chart->complete();

?>
