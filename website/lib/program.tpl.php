<?   // Copyright (c) Isaac Gouy 2004-2014 ?>

<? 
MkMenuForm($Tests,$SelectedTest,$Langs,$SelectedLang,$Id);
$TestName = $Tests[$SelectedTest][TEST_NAME];
$TestTag = $Tests[$SelectedTest][TEST_TAG];
$LangName = $Langs[$SelectedLang][LANG_FULL];
$SourceCodeName = $TestName.' '.$LangName.IdName($Id).' program';
?>

<h2><a href="#measurements" name="measurements">&nbsp;performance measurements</a></h2>

<p>Each table row shows performance measurements for this <?=$LangName;?> program with a particular <a href="<?=CORE_SITE;?>play.html#inputvalue" title="? Help">command-line input value N</a>.</p>

<table>
<colgroup span="4" class="num"></colgroup>
<tr>
<th><a href="<?=CORE_SITE;?>play.html#process" title="How do you measure the programs?">&nbsp;N&nbsp;</a></th>
<th><a href="<?=CORE_SITE;?>play.html#time" title="How do you measure program CPU secs?">CPU&nbsp;secs</a></th>
<th><a href="<?=CORE_SITE;?>play.html#time" title="How do you measure program Elapsed secs?">Elapsed&nbsp;secs</a></th>
<th><a href="<?=CORE_SITE;?>play.html#memory" title="How do you measure program Memory KB?">Memory&nbsp;KB</a></th>
<th><a href="<?=CORE_SITE;?>play.html#gzbytes" title="How do you measure program Source Code Bytes?">Code&nbsp;B</a></th>
<th><a href="<?=CORE_SITE;?>play.html#cpuloadpercent" title="How do you measure CPU Load?">&asymp;&nbsp;CPU&nbsp;Load</a></th>
</tr>
<?

foreach($Data as $d){
      if ($Id==$d[DATA_ID]){
         if ($d[DATA_TESTVALUE]>0){ $n = number_format((double)$d[DATA_TESTVALUE]); } else { $n = '&nbsp;'; }
         if ($d[DATA_STATUS]<0){
            $kb = '&nbsp;'; $fullcpu = '&nbsp;';$elapsed = '&nbsp;'; $load = '&nbsp;';
            $fullcpu = StatusMessage($d[DATA_STATUS]);
         } else {
            if ($d[DATA_MEMORY]==0){
               $kb = '?';
            } else {
               if ($TestName=='startup'){ $kb = '&nbsp;'; }
               else { $kb = number_format((double)$d[DATA_MEMORY]); }
            }
            $fullcpu = number_format($d[DATA_FULLCPU],2);
            $elapsed = ElapsedTime($d);
            $load = CpuLoad($d);
         }

         printf('<tr class="a"><td class="r"><span class="numN">%s</span></td><td class="r">%s</td><td class="r">%s</td><td class="r">%s</td><td class="r">%d</td><td class="smaller">&nbsp;&nbsp;%s</td></tr>',
            $n,$fullcpu,$elapsed,$kb,$d[DATA_GZ],$load); echo "\n";
            
         if ($d[DATA_STATUS]<0){ break; }
      }
}
?>
</table>

<p>Read the <a href="#log">&darr;&nbsp;make, command line, and program output logs</a> to see how this program was run.</p>

<p>Read <a href="performance.php?test=<?=$SelectedTest;?>#about" title="<?=$TestTag;?>"><?=$TestName;?>&nbsp;benchmark</a> to see what this program should do.</p>

<h3><a href="#about" name="about">&nbsp;notes</a></h3><?=$About;?>

<h2><a href="#sourcecode" name="sourcecode">&nbsp;<?=$SourceCodeName;?> source code</a></h2>

<pre><?=$Code;?></pre>

<h3><a href="#log" name="log">&nbsp;make, command-line, and program output logs</a></h3>
<pre><?=$Log;?></pre>


