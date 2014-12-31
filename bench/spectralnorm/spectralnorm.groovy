/* The Computer Language Benchmarks Game
   http://shootout.alioth.debian.org/
   contributed by Jochen Hinrichsen
   modified by Keegan Witt
*/

import groovy.transform.CompileStatic

import java.text.NumberFormat

@CompileStatic
class SpectralNorm {
    static void main(String[] args) {
        int n = (args.length == 0 ? 100 : args[0].toInteger())
        NumberFormat nf = NumberFormat.getInstance()
        nf.setMaximumFractionDigits(9)
        nf.setMinimumFractionDigits(9)
        nf.setGroupingUsed(false)
        println(nf.format(approximate(n)))
    }

    static double approximate(int n) {
        // create unit vector
        List<Double> u = [1.0D] * n

        // 20 steps of the power method
        List<Double> v = [0.0D] * n

        for (int i in 1..10) {
            MultiplyAtAv(n, u, v)
            MultiplyAtAv(n, v, u)
        }

        // B=AtA         A multiplied by A transposed
        // v.Bv /(v.v)   eigenvalue of v
        double vBv = 0.0D
        double vv = 0.0D
        for (int i in 0..<n) {
            vBv += u[i] * v[i]
            vv += v[i] * v[i]
        }

        return Math.sqrt(vBv / vv)
    }

    /* return element i,j of infinite matrix A */
    static double A(double i, double j) {
        return (1.0D) / ((i + j) * (i + j + (1.0D)) / (2.0D) + i + (1.0D))
    }

    /* multiply vector v by matrix A */
    static void MultiplyAv(int n, List<Double> v, List<Double> Av) {
        for (int i in 0..<n) {
            Av[i] = 0.0D
            for (int j in 0..<n)
                Av[i] = Av[i] + A(i, j) * v[j]
        }
    }

    /* multiply vector v by matrix A and then by matrix A transposed */
    static void MultiplyAtAv(int n, List<Double> v, List<Double> AtAv) {
        List<Double> u = [0.0D] * n
        MultiplyAv(n, v, u)
        MultiplyAv(n, u, AtAv)
    }
}
