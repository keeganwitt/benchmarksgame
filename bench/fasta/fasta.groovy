/* The Computer Language Benchmarks Game
   http://shootout.alioth.debian.org/
   contributed by Jochen Hinrichsen
   modified by Keegan Witt
*/

import groovy.transform.CompileStatic

@CompileStatic
class Fasta {
    static final byte NEWLINE = "\n".getBytes()[0]
    static final int LINE_LENGTH = 60
    // Weighted selection from alphabet
    static final String ALU = "GGCCGGGCGCGGTGGCTCACGCCTGTAATCCCAGCACTTTGG" +
              "GAGGCCGAGGCGGGCGGATCACCTGAGGTCAGGAGTTCGAGA" +
              "CCAGCCTGGCCAACATGGTGAAACCCCGTCTCTACTAAAAAT" +
              "ACAAAAATTAGCCGGGCGTGGTGGCGCGCGCCTGTAATCCCA" +
              "GCTACTCGGGAGGCTGAGGCAGGAGAATCGCTTGAACCCGGG" +
              "AGGCGGAGGTTGCAGTGAGCCGAGATCGCGCCACTGCACTCC" +
              "AGCCTGGGCGACAGAGCGAGACTCCGTCTCAAAAA"
    static final byte[] ALUB = ALU.getBytes()
    static final List<Frequency> IUB = [
            new Frequency(s:'a', p:0.27d),
            new Frequency(s:'c', p:0.12d),
            new Frequency(s:'g', p:0.12d),
            new Frequency(s:'t', p:0.27d),
            new Frequency(s:'B', p:0.02),
            new Frequency(s:'D', p:0.02),
            new Frequency(s:'H', p:0.02),
            new Frequency(s:'K', p:0.02),
            new Frequency(s:'M', p:0.02),
            new Frequency(s:'N', p:0.02),
            new Frequency(s:'R', p:0.02),
            new Frequency(s:'S', p:0.02),
            new Frequency(s:'V', p:0.02),
            new Frequency(s:'W', p:0.02),
            new Frequency(s:'Y', p:0.02)
    ]
    static final List<Frequency> HomoSapiens = [
            new Frequency(s:'a', p:0.3029549426680d),
            new Frequency(s:'c', p:0.1979883004921d),
            new Frequency(s:'g', p:0.1975473066391d),
            new Frequency(s:'t', p:0.3015094502008d)
    ]
    static final int BUFFER_SIZE = 8192
    static final int index = 0
    static final byte[] bbuffer = new byte[BUFFER_SIZE]

    // pseudo-random number generator
    static final int IM = 139968
    static final int IA = 3877
    static final int IC = 29573

    static int last = 42

    static void main(String[] args) {
        makeCumulative(HomoSapiens)
        makeCumulative(IUB)

        int n = args.length == 0 ? 2500000 : args[0].toInteger()
        OutputStream out = new BufferedOutputStream(System.out)

        makeRepeatFasta("ONE", "Homo sapiens alu", ALU, n * 2, out)
        makeRandomFasta("TWO", "IUB ambiguity codes", IUB, n * 3, out)
        makeRandomFasta("THREE", "Homo sapiens frequency", HomoSapiens, n * 5, out)
        out.flush()
    }

    static double random(double max) {
        last = (last * IA + IC) % IM
        max * last / IM
    }

    static void makeCumulative(List<Frequency> a) {
        double cp = 0.0d
        for (int i in 0..<a.size()) {
            cp += a[i].p
            a[i].p = cp
        }
    }

    // select a random frequency.c
    static byte selectRandom(List<Frequency> a) {
        int len = a.size()
        double r = random(1.0d)
        for (int i in 0..<len)
            if (r < a[i].p)
                return a[i].c
        return a[len - 1].c
    }

    static void makeRepeatFasta(String id, String desc, String alu, int n, OutputStream writer) {
        int index = 0
        int m = 0
        int k = 0
        int kn = ALUB.length
        writer << ">" + id + " " + desc + "\n"
        while (n > 0) {
            m = (n < LINE_LENGTH) ? n : LINE_LENGTH
            if (BUFFER_SIZE - index < m){
                writer.write(bbuffer, 0, index)
                index = 0
            }
            for (int i in 0..<m) {
                if (k == kn)
                    k = 0
                bbuffer[index++] = ALUB[k]
                k++
            }
            bbuffer[index++] = NEWLINE
            n -= LINE_LENGTH
        }
        if(index != 0)
            writer.write(bbuffer, 0, index)
    }

    static void makeRandomFasta(String id, String desc, List<Frequency> a,  int n, OutputStream writer) {
        int index = 0
        int m = 0
        writer << ">" + id + " " + desc + "\n"
        while (n > 0) {
            m = (n < LINE_LENGTH) ? n : LINE_LENGTH
            if (BUFFER_SIZE - index < m){
                writer.write(bbuffer, 0, index)
                index = 0
            }
            for (int i in 0..<m) {
                bbuffer[index++] = selectRandom(a)
            }
            bbuffer[index++] = NEWLINE
            n -= LINE_LENGTH
        }
        if(index != 0)
            writer.write(bbuffer, 0, index)
    }

    public static class Frequency {
        String s
        byte c
        double p
        // Store string representation as Byte
        public void setS(String rep) {
            s = rep
            // Cannot call def method from here, aborts without warning/ error
            c = rep.getBytes()[0]
        }
    }
}
