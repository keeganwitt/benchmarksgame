<?php
// Copyright (c) Isaac Gouy 2004-2010

// DATA LAYOUT ///////////////////////////////////////////////////

define('DATA_TEST',0);
define('DATA_LANG',1);
define('DATA_ID',2);
define('DATA_TESTVALUE',3);
define('DATA_GZ',4);
define('DATA_FULLCPU',5);
define('DATA_MEMORY',6);
define('DATA_STATUS',7);
define('DATA_LOAD',8);
define('DATA_ELAPSED',9);

// With quad-core we changed from CPU Time to Elapsed Time
// but we still want to show the old stuff
if (SITE_NAME == 'debian' || SITE_NAME == 'gp4'){
   define('DATA_TIME',DATA_FULLCPU);
} else {
   define('DATA_TIME',DATA_ELAPSED);
}


define('N_TEST',0);
define('N_LANG',1);
define('N_ID',2);
define('N_NAME',3);
define('N_FULL',4);
define('N_HTML',5);
define('N_FULLCPU',6);
define('N_MEMORY',7);
define('N_CPU_MAX',8);
define('N_MEMORY_MAX',9);
define('N_COLOR',10);

define('N_N',3);
define('N_LINES',8);
define('N_GZ',9);
define('N_EXCLUDE',10);

define('CPU_MIN',0);
define('MEM_MIN',1);
define('GZ_MIN',2);

define('SCORE_RATIO',0);
define('SCORE_MEAN',1);
define('SCORE_TESTS',2);


// CONSTANTS ///////////////////////////////////////////////////

//define('EXCLUDED','X');
define('PROGRAM_TIMEOUT',-1);
define('PROGRAM_ERROR',-2);
define('PROGRAM_SPECIAL','-3');
define('PROGRAM_EXCLUDED',-4);
define('LANGUAGE_EXCLUDED',-5);
define('NO_COMPARISON',-6);
define('NO_PROGRAM_OUTPUT',-7);


// FUNCTIONS ///////////////////////////////////////////////////

function CompareLangName($a, $b){
   return strcasecmp($a[LANG_FULL],$b[LANG_FULL]);
}

function CompareTestName($a, $b){
   return strcasecmp($a[TEST_NAME],$b[TEST_NAME]);
}

function ExcludeData($d,$langs,$Excl){
   if( !isset($langs[$d[DATA_LANG]]) ) { return LANGUAGE_EXCLUDED; }

   $key = $d[DATA_TEST].$d[DATA_LANG].strval($d[DATA_ID]);
   if (isset($Excl[$key])){
      if ($Excl[$key]){ return PROGRAM_EXCLUDED; }
      else { return PROGRAM_SPECIAL; }
   }
   return $d[DATA_STATUS];
}


// CONTENT ///////////////////////////////////////////////////

function MkMenuForm($Tests,$SelectedTest,$Langs,$SelectedLang){
   echo '<form method="get" action="benchmark.php">', "\n";
   echo '<p><select name="test">', "\n";
   echo '<option value="all">- all ', TESTS_PHRASE, 's -</option>', "\n";

   foreach($Tests as $Row){
      $Link = $Row[TEST_LINK];
      $Name = $Row[TEST_NAME];
      if ($Link==$SelectedTest){
         $Selected = 'selected="selected"';
      } else {
         $Selected = '';
      }
      printf('<option %s value="%s">%s</option>', $Selected,$Link,$Name); echo "\n";
   }
   echo '</select>', "\n";

   echo '<select name="lang">', "\n";
   echo '<option value="all">- all ', LANGS_PHRASE, 's -</option>', "\n";
   foreach($Langs as $Row){
      $Link = $Row[LANG_LINK];
      $Name = $Row[LANG_FULL];
      if ($Link==$SelectedLang){
         $Selected = 'selected="selected"';
      } else {
         $Selected = '';
      }
      printf('<option %s value="%s">%s</option>', $Selected,$Link,$Name); echo "\n";
   }
   echo '</select>', "\n";
   echo '<input type="submit" value="Show" />', "\n";
   echo '</p></form>', "\n";
}


function MkTestsMenuForm($Tests,$SelectedTest,$Action='benchmarks.php'){
   echo '<form method="get" action="benchmark.php">', "\n";
   echo '<p><select name="test">', "\n";
   foreach($Tests as $Row){
      $Link = $Row[TEST_LINK];
      $Name = $Row[TEST_NAME];
      if ($Link==$SelectedTest){
         $Selected = 'selected="selected"';
      } else {
         $Selected = '';
      }
      printf('<option %s value="%s">%s</option>', $Selected,$Link,$Name); echo "\n";
   }
   echo '</select>', "\n";
   echo '<input type="submit" value="Show" />', "\n";
   echo '</p></form>', "\n";
}


function PFx($d){
   if ($d>9.9){ return number_format($d); }
   elseif ($d>0.0){ return number_format($d,1); }
   else { return "&nbsp;"; }
}

function PTime($d){
   if ($d <= 0.0){ return ''; }
   if ($d<300.0){ return number_format($d,2); }
   elseif ($d<3600.0){
     $m = floor($d/60); $s = $d-($m*60); $ss = number_format($s,0);
     if (strlen($ss)<2) { $ss = "0".$ss; }
     return number_format($m,0)."&nbsp;min"; }
   else {
     $h = floor($d/3600); $m = floor(($d-($h*3600))/60);
     $mm = number_format($m,0); if (strlen($mm)<2) { $mm = "0".$mm; }
     return number_format($h,0)."h&nbsp;".$mm."&nbsp;min";
   }
}


function MarkTime($PathRoot=''){
   if (SITE_NAME == 'debian'){
      $Mark = 'late 2007';
   } elseif (SITE_NAME == 'gp4'){
      $Mark = 'mid 2008';
   } else {
      $mtime = filemtime($PathRoot.DATA_PATH.'data.csv');
      $Mark = gmdate("d M Y", $mtime);
   }
   return $Mark;
}

function StatusMessage($i){
   if ($i==0){ $m = ''; }
   elseif ($i==PROGRAM_TIMEOUT){ $m = 'Timed&nbsp;Out'; }
   elseif ($i==PROGRAM_ERROR){ $m = 'Failed'; }
   elseif ($i==NO_COMPARISON){ $m = 'No&nbsp;Comparison'; }
   elseif ($i==-10){ $m = 'Bad&nbsp;Output'; }
   elseif ($i==-11){ $m = 'Make&nbsp;Error'; }
   elseif ($i==-12){ $m = 'Empty'; }
   else { $m = ''; }
   return $m;
}


function ElapsedTime($d){
   if ($d[DATA_ELAPSED] > 0.0){
      return number_format($d[DATA_ELAPSED],2);
   } else {
      return '';
   }
}


function CpuLoad($d){
   if (strlen($d[DATA_LOAD])>1){
      return str_replace(' ','&nbsp;',$d[DATA_LOAD]);
   } else {
      return '';
   }
}

?>
