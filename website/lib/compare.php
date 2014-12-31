<?php
// Copyright (c) Isaac Gouy 2010-2014

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');
require_once(LIB);
require_once(LIB_PATH.'lib_data.php');

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


// FUNCTIONS ///////////////////////////////////////////


function isMulticore($CPULoad){
   // CPU load indicates the program used more than one core
   $sum = 0;
   foreach(explode("%",$CPULoad) as $each){
      $i = (integer)$each;
      if ($i > 0) $sum += $i; 
   }
   return $sum > 120;
}



function BestRows($rows){
   $testvalue = -2; // assume not test value is < 0
   $time = 360000.0; // assume no program was allowed to run for 100 hours
   $id = -2; // assume no id is < 0
   $failed_id = 0;

   // Identify id of fastest row at largest n, or whatever rows there are
   foreach($rows as $row) {
      $row_testvalue = $row[DATA_TESTVALUE];
      if ($row[DATA_STATUS] > PROGRAM_TIMEOUT){
         if ($row_testvalue > $testvalue){
            $testvalue = $row_testvalue;
            $time = $row[DATA_TIME];
            $id = $row[DATA_ID];
         } elseif ($row_testvalue == $testvalue){
            $row_time = $row[DATA_TIME];
            if ($row_time < $time){
               $time = $row_time;
               $id = $row[DATA_ID];
            }
         }
      } else {
         $failed_id = $row[DATA_ID];
      }
   }
   if ($id < 0){ $id = $failed_id; } // assume no id is < 0

   // filter and return the best of the rows
   $best = array();
   foreach($rows as $row) {
      if ($row[DATA_ID] == $id){
         $best[] = $row;
      }
   }
   return $best;
}


function AccumulateComparableRows($rowsL1,$rowsL2,&$comparable){
   $rowL1 = empty($rowsL1) ? NULL :  $rowsL1[0];
   $rowL2 = empty($rowsL2) ? NULL :  $rowsL2[0];
   $time = NO_VALUE; $mem = NO_VALUE; $gz = NO_VALUE;

   $n = min(sizeof($rowsL1),sizeof($rowsL2));
   if ($n > 0){
      for ($i=1;$i<$n;$i++){
         if ($rowsL1[$i][DATA_STATUS]<=PROGRAM_TIMEOUT || $rowsL2[$i][DATA_STATUS]<=PROGRAM_TIMEOUT){ break; }
         $rowL1 = $rowsL1[$i];
         $rowL2 = $rowsL2[$i];
      }
      if ($rowL1[DATA_STATUS] + $rowL2[DATA_STATUS] > PROGRAM_TIMEOUT){
         if ($rowL2[DATA_TIME]>0.0){ $time = $rowL1[DATA_TIME] / $rowL2[DATA_TIME]; }
         if ($rowL2[DATA_MEMORY]>NO_VALUE){ $mem = $rowL1[DATA_MEMORY] / $rowL2[DATA_MEMORY]; } else { $mem = NO_VALUE; }
         if ($rowL2[DATA_GZ]>0.0){ $gz = $rowL1[DATA_GZ] / $rowL2[DATA_GZ]; }
      }
   }
   $test = isset($rowL1) ? $rowL1[DATA_TEST] : $rowL2[DATA_TEST];
   $comparable[$test] = array($rowL1,$rowL2, DATA_TIME => $time, DATA_MEMORY => $mem, DATA_GZ => $gz);
}


function HeadToHeadData($FileName,$Tests,$Langs,$Incl,$Excl,$L1,$L2,$HasHeading=TRUE){
   $measurements = array();
   $lines = file($FileName);

   $prefixL1 = ','.$L1.',';
   $prefixL2 = ','.$L2.',';
   $rowsL1 = array();
   $rowsL2 = array();
   foreach($lines as $line) {
      $isLang1 = strpos($line,$prefixL1);
      if ($isLang1 || strpos($line,$prefixL2)){
         $row = explode( ',', $line);
         $test = $row[DATA_TEST];
// do we need to check $test here?  
//         if (isset($Incl[$test])){
            if (isset($previous) && $previous != $test){ // assume ndata.csv is sorted by test
               AccumulateComparableRows(BestRows($rowsL1),BestRows($rowsL2),$measurements);
               $rowsL1 = array();
               $rowsL2 = array();
            }
            $previous = $test;

            $key = $test.$row[DATA_LANG].$row[DATA_ID];
            settype($row[DATA_ID],'integer');
            // $L1 and $L2 have already been checked
            if (!isset($Excl[$key])){
               settype($row[DATA_STATUS],'integer');
               settype($row[DATA_TESTVALUE],'integer');
               settype($row[DATA_TIME],'double');
               settype($row[DATA_GZ],'double');
               settype($row[DATA_MEMORY],'double');

               if ($isLang1){
                  $rowsL1[] = $row;
               } else {
                  $rowsL2[] = $row;
               }
            }
//         }
      }
   }
   AccumulateComparableRows(BestRows($rowsL1),BestRows($rowsL2),$measurements);

   // collect values for chart and stats
   $ratios = array();
   $times = array();
   $mismatches = array();
   foreach($measurements as $v){
      $test = $v[0][DATA_TEST]; 
      if (($test!=NULL && $Tests[$test][TEST_WEIGHT]<=0) || $v[DATA_TIME] == NO_VALUE){ continue; }
      $ratios[] = $v[DATA_TIME];

      // Too many confuse default memory use with program memory use
      if ($test=='binarytrees'||$test=='regexdna'||
            $test=='revcomp'||$test=='knucleotide'){ 
         $ratios[] = $v[DATA_MEMORY]; }
      else { 
         $ratios[] = 0.0; }

      $ratios[] = $v[DATA_GZ];
      $times[] = $v[DATA_TIME];
      $mismatches[$test] = isMulticore($v[0][DATA_LOAD]) != isMulticore($v[1][DATA_LOAD]);
   }

   // sort by x times faster
   uasort($measurements,'CompareTimeRatio');

   $sorted = array();
   foreach($measurements as $rows){
      $test = isset($rows[0]) ? $rows[0][DATA_TEST] : $rows[1][DATA_TEST];
      $sorted[$test] = $rows;
   }
   foreach($Tests as $k => $v){
      if (!isset($sorted[$k])){ $sorted[$k] = array(); }
   }
   
   $stats = Percentiles($times);

   return array($sorted,$ratios,$stats,$mismatches);
}


function CompareTimeRatio($a, $b){
   return $a[DATA_TIME] == NO_VALUE ? 1 :
         ($b[DATA_TIME] == NO_VALUE ? -1 :
         ($a[DATA_TIME] < $b[DATA_TIME] ? -1 : 1));
}

function Percentiles($a){
   sort($a);
   $n = sizeof($a);
   $mid = floor($n / 2);
   if ($n % 2 != 0){
      $median = $a[$mid];
      $lower = Median( array_slice($a,0,$mid+1) ); // include median in both quartiles
      $upper = Median( array_slice($a,$mid) );
   } else {
      $median = ($a[$mid-1] + $a[$mid]) / 2.0;
      $lower = Median( array_slice($a,0,$mid) );
      $upper = Median( array_slice($a,$mid) );
   }
   $maxwhisker = ($upper - $lower) * 1.5;
   $xlower = ($lower - $maxwhisker < $a[0]) ? $a[0]: $lower - $maxwhisker;
   $xupper = ($upper + $maxwhisker > $a[$n-1]) ? $a[$n-1] : $upper + $maxwhisker;

   return array($a[0],$xlower,$lower,$median,$upper,$xupper,$a[$n-1],$n);
}

function Median($a){
   $n = sizeof($a);
   $mid = floor($n / 2);
   return ($n % 2 != 0) ? $a[$mid] : ($a[$mid-1] + $a[$mid]) / 2.0;
}


// PAGE ////////////////////////////////////////////////

$Page = & new Template(LIB_PATH);
$Body = & new Template(LIB_PATH);
$PageId = 'headtohead';
$TemplateName = 'compare.tpl.php';


// GET_VARS ////////////////////////////////////////////////

list($Incl,$Excl) = WhiteListInEx();
$Tests = WhiteListUnique('test.csv',$Incl); // assume test.csv in name order
$Langs = WhiteListUnique('lang.csv',$Incl); // assume lang.csv in name order


// if compare.php was requested directly get the request parameters
if (isset($_GET['lang'])
      && strlen($_GET['lang']) && (strlen($_GET['lang']) <= NAME_LEN)){
   $X = $_GET['lang'];
   if (ereg("^[a-z0-9]+$",$X)){ $L = $X; }
}
if (isset($L)){
   $Available = isset($Langs[$L]) && isset($Incl[$L]);
   // specific request which is not available
   if (!$Available){ 
      $L = 'java'; 
   }
} else {
   // no specific request
   $L = 'java';
}

if (isset($_GET['lang2'])
      && strlen($_GET['lang2']) && (strlen($_GET['lang2']) <= NAME_LEN)){
   $X = $_GET['lang2'];
   if (ereg("^[a-z0-9]+$",$X)){ $L2 = $X; }
}
if ($Available && !empty($L2)){ 
   $Available = isset($Langs[$L2]) && isset($Incl[$L2]) && ($L2 != $L);
   if (!$Available){ 
      // assume LANG_COMPARE is always available in every data set
      $L2 = $Langs[$L][LANG_COMPARE];
   }
} else {
      // assume LANG_COMPARE is always available in every data set
      $L2 = $Langs[$L][LANG_COMPARE];
}



// HEADER ////////////////////////////////////////////////

$mark = MarkTime();
$mark = $mark.' '.SITE_NAME;

$LangName = $Langs[$L][LANG_FULL];
$LangName2 = $Langs[$L2][LANG_FULL];
$Title = ($Available) ? $LangName.'&nbsp;vs&nbsp;'.$LangName2 : 'Not Available';

$bannerUrl = CORE_SITE;
$faqUrl = CORE_SITE.'play.html';


// DATA ////////////////////////////////////////////////

$Data = HeadToHeadData(DATA_PATH.'ndata.csv',$Tests,$Langs,$Incl,$Excl,$L,$L2);

$timeUsed = 'Elapsed secs';


// ABOUT ////////////////////////////////////////////////

$About = & new Template(ABOUT_PATH);
$AboutTemplateName = $L.SEPARATOR.'about.tpl.php';
if (! file_exists(ABOUT_PATH.$AboutTemplateName)){ $AboutTemplateName = 'blank-about.tpl.php'; }
$About->set('Version', HtmlFragment(VERSION_PATH.$L.SEPARATOR.'version.php'));


// META ////////////////////////////////////////////////

$MetaKeywords = '<meta name="description" content="'.$LangName.' programs vs '.$LangName2.' programs (performance on '.PLATFORM_NAME.')." />';
         
// if the URL parameter is not currently accepted then do not index
if ($Available) { 
   $canonicalPage = !(isset($LinkRelCanonical) && !(empty($LinkRelCanonical)));
   if ($canonicalPage){ 
      $metaRobots = '<meta name="robots" content="index,follow,archive" />'; 
   } else { 
      $metaRobots = '<meta name="robots" content="index,nofollow,noarchive" />'; 
   }
} else {
   $metaRobots = '<meta name="robots" content="noindex,nofollow" />';
   $canonicalPage = FALSE;
}




// TEMPLATE VARS ////////////////////////////////////////////////

$Page->set('PageTitle', $Title.BAR.'Computer&nbsp;Language&nbsp;Benchmarks&nbsp;Game');
$Page->set('BannerTitle', BANNER_TITLE);
$Page->set('BannerUrl', $bannerUrl);
$Page->set('FaqTitle', FAQ_TITLE);
$Page->set('FaqUrl', $faqUrl);

$Body->set('Tests', $Tests);
$Body->set('Langs', $Langs);
$Body->set('SelectedLang', $L);
$Body->set('SelectedLang2', $L2);
$Body->set('Excl', $Excl);
$Body->set('Mark', $mark);
$Body->set('TimeUsed', $timeUsed);
$Body->set('Data', $Data );
$Body->set('CanonicalPage', $canonicalPage);

$About->set('SelectedLang', $L);

$Body->set('About', $About->fetch($AboutTemplateName));
$Page->set('PageBody', $Body->fetch($TemplateName));
$Page->set('Robots', $metaRobots);
$Page->set('MetaKeywords', $MetaKeywords);
if (isset($LinkRelCanonical)) { $Page->set('LinkCanonical', $LinkRelCanonical); }
$Page->set('PageId', $PageId);

echo $Page->fetch('page.tpl.php');
?>
