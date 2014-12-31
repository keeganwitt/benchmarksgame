/* The Computer Language Benchmarks Game
   http://shootout.alioth.debian.org/
   contributed by Jochen Hinrichsen
   modified by Keegan Witt
*/

import groovy.transform.CompileStatic

@CompileStatic
class BinaryTrees {
    static void main(String[] args) {
        int n = (args.length == 0) ? 10 : args[0].toInteger()
        int minDepth = 4
        int maxDepth = [ minDepth + 2, n].max()
        int stretchDepth = maxDepth + 1

        int check = (TreeNode.bottomUpTree(0,stretchDepth)).itemCheck()
        println "stretch tree of depth ${stretchDepth}\t check: ${check}"

        TreeNode longLivedTree = TreeNode.bottomUpTree(0,maxDepth)

        int depth = minDepth
        while (depth <= maxDepth) {
            int iterations = 1 << (maxDepth - depth + minDepth)
            check = 0
            for (int i in 1..iterations) {
                check += (TreeNode.bottomUpTree(i, depth)).itemCheck()
                check += (TreeNode.bottomUpTree(-i, depth)).itemCheck()
            }

            println "${iterations*2}\t trees of depth ${depth}\t check: ${check}"
            depth += 2
        }

        println "long lived tree of depth ${maxDepth}\t check: ${longLivedTree.itemCheck()}"
    }

    static class TreeNode {
        TreeNode left
        TreeNode right
        int item

        TreeNode(int item) {
            this.item = item
        }

        private static TreeNode bottomUpTree(int item, int depth) {
            if (depth > 0) {
                depth = depth - 1
                int item2 = item * 2
                return new TreeNode(
                        bottomUpTree(item2 - 1, depth),
                        bottomUpTree(item2, depth),
                        item
                )
            } else {
                return new TreeNode(item)
            }
        }

        TreeNode(TreeNode left, TreeNode right, int item) {
            this.left = left
            this.right = right
            this.item = item
        }

        int itemCheck() {
            // if necessary deallocate here
            if (left == null)
                return item
            else
                return item + left.itemCheck() - right.itemCheck()
        }
    }
}
