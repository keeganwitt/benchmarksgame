<p><b>diff</b> program output N = 1000 with this <a href="iofile.php&amp;test=<?=$SelectedTest;?>&amp;file=output">output file</a> to check your program is correct before contributing.
</p>

<p>Each program should create and keep alive 503 pre-emptive threads, explicity or implicitly linked in a ring, and pass a token between one thread and the next thread at least N times.</p>

<p>We are trying to show the performance of various programming language implementations - so we ask that contributed programs not only give the correct result, but also <b>use the same algorithm</b> to calculate that result.</p>

<p>Each program should</p>
<ul>
   <li>create 503 linked pre-emptive threads (named 1 to 503)</li>
   <li>thread 503 should be linked to thread 1, forming an unbroken ring</li>           
   <li>pass a token to thread 1</li>     
   <li>pass the token from thread to thread N times</li>        
   <li>print the name of the last thread (1 to 503) to take the token</li>
</ul>

<p>Similar benchmarks are described in <a href="http://www.sics.se/~joe/ericsson/du98024.html">Performance Measurements of Threads in Java and Processes in Erlang, 1998;</a> and <a href="http://www.cl.cam.ac.uk/users/mr/Cobench.html">A Benchmark Test for BCPL Style Coroutines, 2004.</a> (Note: 'Benchmarks that may seem to be concurrent are often sequential. The estone benchmark, for instance, is entirely sequential. So is also the most common implementation of the "ring benchmark'; usually <a href="http://www.erlang.org/doc/efficiency_guide/processes.html#id66266">one process is active, while the others wait in a receive statement</a>.')
For some language implementations increasing the number of threads quickly results in <a href="http://www.mozart-oz.org/documentation/apptut/node9.html#chapter.concurrency.cheap">Death by Concurrency</a>.</p>

<p>Programs may use pre-emptive kernel threads or pre-emptive lightweight threads; but programs that use non pre-emptive threads (coroutines, cooperative threads) and any programs that use custom schedulers, will be listed as interesting alternative implementations. Briefly say what concurrency technique is used in the program header comment.</p>
