<?
header("Content-type: image/png");

// Copyright (c) Isaac Gouy 2012

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');
require_once(LIB_PATH.'lib_chart.php');

SetChartCacheControl();

$in = WhiteListIn();
$WhiteListLangs = WhiteListUnique('lang.csv',$in);


// DATA LAYOUT ///////////////////////////////////////////////////

define('STATS_SIZE',8);
define('STAT_MIN',0);
define('STAT_XLOWER',1);
define('STAT_LOWER',2);
define('STAT_MEDIAN',3);
define('STAT_UPPER',4);
define('STAT_XUPPER',5);
define('STAT_MAX',6);
define('STATS_N',7);


// DATA ////////////////////////////////////////////////////

list ($Mark,$valid) = ValidMark(TRUE);
list ($BackText,$valid) = ValidLangs($WhiteListLangs,$valid);
list ($Stats,$valid) = ValidMatrix('s',STATS_SIZE,$valid);
//for ($i=0;$i<sizeof($Stats);$i++) $Stats[$i] = log10($Stats[$i]);

// CHART /////////////////////////////////////////////////////

$chart = new BoxChart();
$chart->yAxis(axis10());

if ($valid){
   $chart->backgroundText($BackText);
   $chart->boxAndWhiskers($Stats);
   $chart->notice($Mark);
}

$chart->xAxisLegend('selected language implementations');
$chart->yAxisLegend('program time � fastest program time');
$chart->title('Ten tiny examples - How many times slower?');
$chart->frame();
$chart->complete();

?>
