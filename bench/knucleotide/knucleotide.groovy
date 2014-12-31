/* The Computer Language Benchmarks Game
   http://shootout.alioth.debian.org/
   contributed by Jochen Hinrichsen
   modified by Keegan Witt
*/

import groovy.transform.CompileStatic

import java.text.DecimalFormat

@CompileStatic
class KNucleotide {
    static void main(String[] args) {
        String sequence = readSequence(new FileInputStream(args[0]), ">THREE").toUpperCase()
        assert sequence.size() > 1000
        [1, 2].each() {
            writeFrequency(sequence, it)
        }
        ["GGT", "GGTA", "GGTATT", "GGTATTTTAATT", "GGTATTTTAATTTATAGT"].each() {
            println "${sequence.count(it)}\t${it}"
        }
    }

    static String readSequence(InputStream streamin, String id) {
        String sequence = ""
        boolean record = false
        streamin.eachLine() { String line ->
            switch (line) {
                case ~"^$id.*":
                    record = true
                    break
                case [~"^>.*", ~"^;.*"]:
                    record = false
                    break
                default:
                    if (record) {
                        sequence += line
                    }
            }
        }
        sequence
    }

    static void writeFrequency(String sequence, int f) {
        Map<String, Integer> count = [:]
        DecimalFormat formatter = new DecimalFormat("#0.000")
        for (int offset in 0..<f) frequency(sequence, f, offset, count)
        // default sort() is smallest first
        // sort for multiple properties: [ it.value, it.key ]
        count.values().sort({ l, r -> r <=> l }).each() { value ->
            def entry = count.find() { entry ->
                entry.getValue() == value
            }
            println "${entry.key} ${formatter.format(100.0 * value / (sequence.size() - f + 1))}"
        }
        println ""
    }

    static void frequency(String sequence, int f, int offset, Map<String, Integer> count) {
        int n = sequence.size()
        int last = n - f + 1
        (offset..<last).step(f) { int i ->
            String key = sequence[i..<i + f]
            // No automatic defaulting
            if (count[key] == null)
                count[key] = 1
            else
                count[key]++
        }
    }
}
