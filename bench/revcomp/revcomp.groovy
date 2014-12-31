/* The Computer Language Benchmarks Game
   http://shootout.alioth.debian.org/
   contributed by Jochen Hinrichsen.
   modified by Keegan Witt
*/

import groovy.transform.CompileStatic

@CompileStatic
class RevComp {
    static void main(String[] args) {
        new File(args[0]).newReader().withReader { reader ->
            StringBuffer segment = new StringBuffer()
            String sequence

            while ((sequence = reader.readLine()) != null) {
                if (sequence.startsWith('>')) {
                    dumpSegment(segment, 60)
                    println sequence
                } else {
                    segment.insert(0, revcomp(sequence))
                }
            }
            dumpSegment(segment, 60)
        }
    }

    static String revcomp(String sequence) {
        Map<String, String> complement = ['A': 'T', 'C': 'G', 'G': 'C', 'T': 'A', 'M': 'K', 'R': 'Y', 'W': 'W', 'S': 'S', 'Y': 'R', 'K': 'M', 'V': 'B', 'H': 'D', 'D': 'H', 'B': 'V', 'N': 'N']
        StringBuffer compseq = new StringBuffer()
        sequence.reverse().toUpperCase().each() {
            compseq.append complement[it]
        }
        compseq.toString()
    }

    static void dumpSegment(StringBuffer segment, int length) {
        int segsize = segment.size()
        if (segsize < 1)
            return
        int start = 0
        int end = length - 1

        while (true) {
            println segment[start..end]
            if (end + length < segsize - 1) {
                start += length
                end += length
            } else {
                println segment[end + 1..segsize - 1]
                break
            }
        }
        segment.setLength(0)
    }
}
