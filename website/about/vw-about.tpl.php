<p><a href="http://www.davethomas.net/papers/ubiquitous1995.pdf">"Ubiquitous Applications: Embedded Systems to Mainframe"</a> <b>pdf</b></p>
<p><a href="http://www.cparity.com/projects/AcmClassification/samples/226261.pdf">"Smalltalk Reaches Crossroads in the Insurance Industry"</a> <b>pdf</b></p>
<p><a href="http://userweb.cs.utexas.edu/~shmat/courses/cs345/ingalls.pdf">"Design Principles Behind Smalltalk"</a> <b>pdf</b></p>
<?=$Version;?>
<p>Home Page: <a href="http://smalltalk.cincom.com/prodinformation/index.ssp?content=vwfactsheet">Cincom Smalltalk&#8482; VisualWorks</a></p>
<p>Download: <a href="http://www.cincomsmalltalk.com/main/developer-community/trying-cincom-smalltalk/try-cincom-smalltalk/">VisualWorksl&#174; Personal&#174;Use</a></p>

<p>Code size measurements are misleading for Smalltalk because source files are usually only used to archive or transfer code. Smalltalk code is created, stored and run in a Smalltalk <em>image</em>. We show Smalltalk source code in a verbose <em>chunk file</em> format used to archive or transfer source code between Smalltalk <em>images</em>.</p>
<p><br/>We've made the Smalltalk code a little more generic by abstracting out these implementation specific details:</p>
<pre>
Object subclass<span class="sym">: #</span>Tests
   instanceVariableNames<span class="sym">:</span> <span class="str">''</span>
   classVariableNames<span class="sym">:</span> <span class="str">''</span>
   poolDictionaries<span class="sym">:</span> <span class="str">''</span>
   category<span class="sym">:</span> <span class="str">'Shootout'</span><span class="sym">!</span>


<span class="sym">!</span>Tests class methodsFor<span class="sym">:</span> <span class="str">'platform'</span><span class="sym">!</span>
arg
   <span class="sym">^</span>CEnvironment commandLine last asNumber<span class="sym">! !</span>

<span class="sym">!</span>Tests class methodsFor<span class="sym">:</span> <span class="str">'platform'</span><span class="sym">!</span>
stdin
   <span class="sym">^</span>Stdin<span class="sym">! !</span>


<span class="sym">!</span>Tests class methodsFor<span class="sym">:</span> <span class="str">'platform'</span><span class="sym">!</span>
stdinSpecial
   <span class="sym">^</span>ExternalReadStream on<span class="sym">:</span>
      <span class="sym">(</span>ExternalConnection ioAccessor<span class="sym">: (</span>UnixDiskFileAccessor new handle<span class="sym">:</span> <span class="num">0</span><span class="sym">))! !</span>


<span class="sym">!</span>Tests class methodsFor<span class="sym">:</span> <span class="str">'platform'</span><span class="sym">!</span>
stdout
   <span class="sym">^</span>Stdout<span class="sym">! !</span>


<span class="sym">!</span>Tests class methodsFor<span class="sym">:</span> <span class="str">'platform'</span><span class="sym">!</span>
stdoutSpecial
   <span class="sym">^</span>ExternalWriteStream on<span class="sym">:</span>
      <span class="sym">(</span>ExternalConnection ioAccessor<span class="sym">: (</span>UnixDiskFileAccessor new handle<span class="sym">:</span> <span class="num">1</span><span class="sym">))! !</span>


<span class="sym">!</span>Stream methodsFor<span class="sym">:</span> <span class="str">'platform'</span><span class="sym">!</span>
nl
   <span class="kwa">self</span> nextPut<span class="sym">:</span> Character lf<span class="sym">! !</span>


<span class="sym">!</span>Stream methodsFor<span class="sym">:</span> <span class="str">'platform'</span><span class="sym">!</span>
print<span class="sym">:</span> number digits<span class="sym">:</span> decimalPlaces
   <span class="kwa">self</span> nextPutAll<span class="sym">:</span>
      <span class="sym">((</span>number asFixedPoint<span class="sym">:</span> decimalPlaces<span class="sym">)</span> printString copyWithout<span class="sym">: $</span>s<span class="sym">)! !</span>


<span class="sym">!</span>Stream methodsFor<span class="sym">:</span> <span class="str">'platform'</span><span class="sym">!</span>
print<span class="sym">:</span> number paddedTo<span class="sym">:</span> width
   number printOn<span class="sym">:</span> <span class="kwa">self</span> paddedWith<span class="sym">: $</span>  to<span class="sym">:</span> width base<span class="sym">:</span> <span class="num">10</span><span class="sym">! !</span>


<span class="sym">!</span>Integer methodsFor<span class="sym">:</span> <span class="str">'platform'</span><span class="sym">!</span>
asFloatD
   <span class="sym">^</span><span class="kwa">self</span> asDouble<span class="sym">! !</span>
</pre>
