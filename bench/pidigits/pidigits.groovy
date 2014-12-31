/* The Computer Language Benchmarks Game
   http://shootout.alioth.debian.org/
   contributed by Jochen Hinrichsen
   modified by Keegan Witt
*/

import groovy.transform.CompileStatic

@CompileStatic
class PiDigits {
    static void main(String[] args) {
        int L = 10
        int n = (args.length == 0) ? 10 : args[0].toInteger()
        Digits digits = new Digits()
        int j = 0
        while (n > 0){
            if (n >= L) {
                for (int i in 0..<L)
                    print(digits.next())
                j += L
            } else {
                for (int i in 0..<n)
                    print(digits.next())
                print(" " * (L-n-1))
                j += n
            }
            print("\t:")
            println j
            n -= L;
        }
    }

    static class T {
        BigInteger q, r, s, t, k = 0G

        T compose(T t2) {
            new T(q: q * t2.q,
                  r: q * t2.r + r * t2.t,
                  s: s * t2.q + t * t2.s,
                  t: s * t2.r + t * t2.t)
        }

        BigInteger extract(BigInteger j) {
            // groovy does not support integer division using /
            (q*j + r).divide(s*j + t)
        }

        T next() {
            k++
            q = k
            r = 4G*k+2G
            s = 0G
            t = 2G*k+1G
            this
        }
    }

    static class Digits {
        T x = new T(q:0G, r:0G, s:0G, t:0G)
        T z = new T(q:1G, r:0G, s:0G, t:1G)

        private T consume(T t) {
            z.compose(t)
        }

        private BigInteger digit() {
            z.extract(3G)
        }

        private boolean isSafe(BigInteger digit) {
            digit == z.extract(4G)
        }

        private T produce(BigInteger y) {
            new T(q:10G, r:-10G*y, s:0G, t:1G).compose(z)
        }

        BigInteger next() {
            BigInteger y = digit()
            if (isSafe(y)) {
                z = produce(y)
                return y
            } else {
                z = consume(x.next())
                return next()
            }
        }
    }
}
