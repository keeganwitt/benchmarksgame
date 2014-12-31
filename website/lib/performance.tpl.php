<?   // Copyright (c) Isaac Gouy 2004-2014 ?>

<? 
// FUNCTIONS ///////////////////////////////////////////

// should these be on the tpl.php?

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

function PFx($d){
   if ($d>9.9){ return number_format($d); }
   elseif ($d>0.0){ return number_format($d,1); }
   else { return "&nbsp;"; }
}


// PAGE ////////////////////////////////////////////////

MkMenuForm($Tests,$SelectedTest,$Langs,NULL);
$Row = $Tests[$SelectedTest];
$TestName = $Row[TEST_NAME];
$TestTag = $Row[TEST_TAG];
$TestLink = $Row[TEST_LINK];

list($Succeeded,$Failed,$Special,$Labels,$Ratios) = $Data;
unset($Data);

$first = 0;
$NString = 'N=?';
foreach($Succeeded as $d){
   if ($d[DATA_TESTVALUE]>0){
      $testValue = (double)$d[DATA_TESTVALUE];
      $NString = 'N='.number_format($testValue);
      break; 
   }
}

// BEWARE - Hard coded values - BEWARE

if ($TestName=='fasta'||$TestName=='k-nucleotide'||
      $TestName=='reverse-complement'||$TestName=='regex-dna'){
   if ($d[DATA_TESTVALUE] == 25000000) { $NString = '&asymp;240MB '.$NString; }
   elseif ($d[DATA_TESTVALUE] == 2500000) { $NString = '&asymp;24MB '.$NString; }
   elseif ($d[DATA_TESTVALUE] == 5000000) { $NString = '&asymp;50MB '.$NString; }
   elseif ($d[DATA_TESTVALUE] == 1000000) { $NString = '&asymp;10MB '.$NString; }
   elseif ($d[DATA_TESTVALUE] == 500000) { $NString = '&asymp;5MB '.$NString; }
   }

if ($TestName=='startup'){ $NString = ''; }


// Use the table column headers to emphasize the row sort order
$CPU_sort_td = '<td><a href="performance.php?test='.$SelectedTest.'&amp;sort=fullcpu" title="Sort by CPU Time secs">sort</a></td>';
$MEM_sort_td = '<td><a href="performance.php?test='.$SelectedTest.'&amp;sort=kb" title="Sort by Memory KB">sort</a></td>';
$ELAPSED_sort_td = '<td><a href="performance.php?test='.$SelectedTest.'&amp;sort=elapsed" title="Sort by Elapsed Time secs">sort</a></td>';
$GZBYTES_sort_td = '<td><a href="performance.php?test='.$SelectedTest.'&amp;sort=gz" title="Sort by Source Code Bytes">sort</a></td>';

if ($Sort=='fullcpu'){
   $CPU_sort_td = '<td>&nbsp;</td>';
   $Chart_intro_1 = '<i>how many times slower</i>';
   $Chart_intro_2 = 'program was, compared to the fastest program';
} elseif ($Sort=='kb'){
   $MEM_sort_td = '<td>&nbsp;</td>';
   $Chart_intro_1 = '<i>how many times more</i> Memory';
   $Chart_intro_2 = 'program used, compared to the program that used least Memory';
} elseif ($Sort=='elapsed'){
   $ELAPSED_sort_td = '<td>&nbsp;</td>';
   $Chart_intro_1 = '<i>how many times slower</i>';
   $Chart_intro_2 = 'program was, compared to the fastest program';
} elseif ($Sort=='gz'){ 
   $GZBYTES_sort_td = '<td>&nbsp;</td>';
   $Chart_intro_1 = '<i>how many times more</i> Code';
   $Chart_intro_2 = 'program used, compared to the program that used least Code';
}
?>  


<h2><a href="#chart" name="chart">&nbsp;<?=$TestName;?>&nbsp;<strong>benchmark</strong></a>&nbsp;<a href="<?=CORE_SITE;?>play.html#inputvalue"><?=$NString;?></a></h2>

<p>Each chart bar shows <?=$Chart_intro_1;?>, one <a href="#about" title="Read about the <?=$TestName;?> benchmark">&darr;&nbsp;<b><?=$TestName;?></b></a> <?=$Chart_intro_2;?>.</p>

<p><img src="chart.php?<?='r='.Encode($Ratios);?>&amp;<?='m='.Encode($Mark);?>&amp;<?='w='.Encode($Labels);?>&amp;<?='ww='.Encode($TestLink);?>&amp;<?='so='.Encode($Sort);?>"
   alt=""
   title=""
   width="480" height="300"
 /></p>


<p>These are not the only programs that could be written. These are not the only compilers and interpreters. These are not the only programming languages.</p>

<p>Column &#215; shows <i>how many times more</i> each program used compared to the benchmark program that used least.</p>

<table>
<colgroup span="2" class="txt"></colgroup>
<colgroup span="3" class="num"></colgroup>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?
echo $CPU_sort_td;
echo $ELAPSED_sort_td;
echo $MEM_sort_td;
echo $GZBYTES_sort_td;
?>
</tr>

<tr>
<th>&nbsp;&nbsp;&#215;&nbsp;&nbsp;</th>
<th>Program&nbsp;Source&nbsp;Code</th>
<th><a href="<?=CORE_SITE;?>play.html#time" title="How do you measure program CPU secs?">CPU&nbsp;secs</a></th>
<th><a href="<?=CORE_SITE;?>play.html#time" title="How do you measure program Elapsed secs?">Elapsed&nbsp;secs</a></th>
<th><a href="<?=CORE_SITE;?>play.html#memory" title="How do you measure program Memory KB?">Memory&nbsp;KB</a></th>
<th><a href="<?=CORE_SITE;?>play.html#gzbytes" title="How do you measure program Source Code Bytes?">Code&nbsp;B</a></th>
<th><a href="<?=CORE_SITE;?>play.html#cpuloadpercent" title="How do you measure CPU Load?">&asymp;&nbsp;CPU&nbsp;Load</a></th>
</tr>

<?
foreach($Langs as $k => $v){ $No_Program_Langs[$k] = TRUE; }

$better = array();
if (sizeof($Succeeded) > 0){ $first = $Succeeded[0]; }

foreach($Succeeded as $d){
   $k = $d[DATA_LANG];

   $CPU = '';
   $MEM = '';
   $ELAPSED = '';
   $GZBYTES = '';
   if (!isset($better[$k])){
      $better[$k] = TRUE;
      // Sort according to current sort criteria, bold the sort-column
      if ($Sort=='fullcpu'){
         $CPU = ' class="sort"';
      } elseif ($Sort=='kb'){
         $MEM = ' class="sort"';
      } elseif ($Sort=='elapsed'){
         $ELAPSED = ' class="sort"';
      } elseif ($Sort=='gz'){ 
         $GZBYTES = ' class="sort"';
      }
   }

   if ($Sort=='fullcpu'){   
      if ($first[DATA_FULLCPU]==0){ $ratio = 0; }
      else { $ratio = $d[DATA_FULLCPU]/$first[DATA_FULLCPU]; }
   } elseif ($Sort=='kb'){ 
      if (($TestName=='startup')||($first[DATA_MEMORY]==0)){ $ratio = 0; }
      else { $ratio = $d[DATA_MEMORY]/$first[DATA_MEMORY]; }
   } elseif ($Sort=='elapsed'){
      if ($first[DATA_ELAPSED]==0){ $ratio = 0; }
      else { $ratio = $d[DATA_ELAPSED]/$first[DATA_ELAPSED]; }
   } elseif ($Sort=='gz'){
      if ($first[DATA_GZ]==0){ $ratio = 0; }
      else { $ratio = $d[DATA_GZ]/$first[DATA_GZ]; }
   }

   unset($No_Program_Langs[$k]);
   $Name = $Langs[$k][LANG_FULL];
   $HtmlName = $Langs[$k][LANG_HTML].IdName($d[DATA_ID]);
   $TipName = $Name.IdName($d[DATA_ID]);

   $id = $d[DATA_ID];
   $fullcpu = $d[DATA_FULLCPU];
   if ($TestName=='startup'){
      $fc = number_format($fullcpu/$testValue,4);
      $kb = '&nbsp;';
      $e = '&nbsp;';
      $ld = '&nbsp;';
   } else { 
      $fc = PTime($fullcpu);
      if ($d[DATA_MEMORY]==0){ $kb = '?'; } else { $kb = number_format((double)$d[DATA_MEMORY]); }
      $e = PTime($d[DATA_ELAPSED]);
      $ld = CpuLoad($d);

   }
   $gz = $d[DATA_GZ];

   printf('<tr>'); echo "\n";
   printf('<td>%s</td><td><a href="program.php?test=%s&amp;lang=%s&amp;id=%d" title="Read the Program Source Code : %s">%s</a></td>',
      PFx($ratio),$SelectedTest,$k,$id,$TipName,$HtmlName); echo "\n";

   printf('<td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%d</td><td class="smaller">&nbsp;&nbsp;%s</td>',
         $CPU, $fc, $ELAPSED, $e, $MEM, $kb, $GZBYTES, $gz, $ld); echo "\n";

   echo "</tr>\n";
}
unset($better);
?>

<?
foreach($Langs as $k => $v){
   foreach($Failed as $d){
      if ($d[DATA_LANG]==$k){
         printf('<tr>'); echo "\n";
         $Name = $v[LANG_FULL];
         $HtmlName = $Langs[$k][LANG_HTML].IdName($d[DATA_ID]);
         $TipName = $Name.IdName($d[DATA_ID]);

         $id = $d[DATA_ID];
         $fullcpu = $d[DATA_FULLCPU];
         $gz = $d[DATA_GZ];

         if ($d[DATA_STATUS]==PROGRAM_TIMEOUT){
            $ratio = ''; $e = PTime($d[DATA_ELAPSED]);
         } else { $ratio = ''; $e = ''; }


         printf('<td>%s</td><td><a href="program.php?test=%s&amp;lang=%s&amp;id=%d" title="Read the Program Source Code : %s">%s</a></td>',
            $ratio,$SelectedTest,$k,$id,$TipName,$HtmlName); echo "\n";

         $message = StatusMessage($d[DATA_STATUS]);
         printf('<td>%s</td><td>%s</td><td></td><td>%s</td><td></td>', $message, $e, $gz);

         echo "</tr>\n";
         unset($No_Program_Langs[$k]);
      }
   }
}
?>

<?
if (sizeof($Special)>0){ 
   printf('<tr><th colspan="7"><a href="#wrong" name="wrong">"wrong" (different) algorithm / less comparable programs</a></th></tr>', CORE_SITE);

   foreach($Special as $d){
      $k = $d[DATA_LANG];
      $Name = $Langs[$k][LANG_FULL];
      $TipName = $Name.IdName($d[DATA_ID]);
      $HtmlName = $Langs[$k][LANG_HTML].IdName($d[DATA_ID]);

      if ($Sort=='fullcpu'){   
         if ($first[DATA_FULLCPU]==0){ $ratio = 0; }
         else { $ratio = $d[DATA_FULLCPU]/$first[DATA_FULLCPU]; }
      } elseif ($Sort=='elapsed'){
         if ($first[DATA_ELAPSED]==0){ $ratio = 0; }
         else { $ratio = $d[DATA_ELAPSED]/$first[DATA_ELAPSED]; }
      } elseif ($Sort=='kb'){
         if (($TestName=='startup')||($first[DATA_MEMORY]==0)){ $ratio = 0; }
         else { $ratio = $d[DATA_MEMORY]/$first[DATA_MEMORY]; }
      } elseif ($Sort=='gz'){
         if ($first[DATA_GZ]==0){ $ratio = 0; }
         else { $ratio = $d[DATA_GZ]/$first[DATA_GZ]; }
      }


      $id = $d[DATA_ID];
      $gz = $d[DATA_GZ];
      $fullcpu = $d[DATA_FULLCPU];
      $status = $d[DATA_STATUS];
      $e = ElapsedTime($d);
      if ($d[DATA_MEMORY]==0){ $kb = '?'; } else { $kb = number_format((double)$d[DATA_MEMORY]); }

      printf('<tr>'); echo "\n";

      if ($status < 0){
         printf('<td>&nbsp;</td><td><a href="program.php?test=%s&amp;lang=%s&amp;id=%d" title="Read the Program Source Code : %s">%s</a></td>',
            $SelectedTest,$k,$id,$TipName,$HtmlName); echo "\n";

         printf('<td>%s</td><td>&nbsp;</td><td>&nbsp;</td><td>%d</td>', StatusMessage($status), $gz);
      }
      else {
         printf('<td>%s</td><td><a href="program.php?test=%s&amp;lang=%s&amp;id=%d" title="Read the Program Source Code : %s">%s</a></td>',
            PFx($ratio),$SelectedTest,$k,$id,$TipName,$HtmlName); echo "\n";
         printf('<td>%0.2f</td><td>%0.2f</td><td>%s</td><td>%d</td><td></td>', $fullcpu, $e, $kb, $gz ); echo "\n";
      }
      echo "</tr>\n";
   }
}
?>


<? // MISSING PROGRAMS TABLE //////////////////////////

if (sizeof($No_Program_Langs)>0){
   echo '<tr><th colspan="7"><a href="#missing" name="missing">missing benchmark programs</a></th></tr>', "\n";
      
   foreach($Langs as $k => $v){
      $no_program = isset($No_Program_Langs[$k]);
      if ($no_program){  
         printf('<tr>'); echo "\n";             
         $Name = $v[LANG_FULL];
         $HtmlName = $v[LANG_HTML];

         printf('<td></td><td><a href="program.php?test=%s&amp;lang=%s">%s</a></td>', 
            $SelectedTest,$k,$HtmlName); echo "\n";

         echo '<td>No&nbsp;program</td><td></td><td></td><td></td><td></td>';
         echo "</tr>\n";     
      }
   }
}
?>
</table>

<h3><a href="#about" name="about">&nbsp;<?=$TestName;?>&nbsp;<strong>benchmark</strong>&nbsp;:&nbsp;<?=$TestTag;?></a></h3>
<?=$About;?>
