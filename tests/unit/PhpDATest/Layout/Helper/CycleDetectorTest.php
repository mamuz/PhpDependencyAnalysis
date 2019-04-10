<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2019 Marco Muths
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpDATest\Layout\Helper;

use Fhaculty\Graph\Graph;
use PhpDA\Layout\Helper\CycleDetector;

class CycleDetectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var CycleDetector */
    protected $fixture;

    /** @var Graph */
    protected $graph;

    protected function setUp()
    {
        $this->graph = new Graph;
        $this->fixture = new CycleDetector;
    }

    public function testDontFindCyles()
    {
        $vertexA = $this->graph->createVertex('A');
        $vertexB = $this->graph->createVertex('B');
        $vertexC = $this->graph->createVertex('C');

        $vertexA->createEdgeTo($vertexB);
        $vertexB->createEdgeTo($vertexC);

        $cycles = $this->fixture->inspect($this->graph)->getCycles();
        self::assertEmpty($cycles);
    }

    public function testFindSimpleCyle()
    {
        $vertexA = $this->graph->createVertex('A');
        $vertexB = $this->graph->createVertex('B');

        $vertexA->createEdgeTo($vertexB);
        $vertexB->createEdgeTo($vertexA);

        $cycles = $this->fixture->inspect($this->graph)->getCycles();

        self::assertCount(1, $cycles);
        self::assertSame('A -> B -> A', $cycles[0]->toString());
    }

    public function testFindDelayedCyle()
    {
        $vertexA = $this->graph->createVertex('A');
        $vertexB = $this->graph->createVertex('B');
        $vertexC = $this->graph->createVertex('C');
        $vertexD = $this->graph->createVertex('D');

        $vertexA->createEdgeTo($vertexB);
        $vertexB->createEdgeTo($vertexC);
        $vertexC->createEdgeTo($vertexB);
        $vertexC->createEdgeTo($vertexD);

        $cycles = $this->fixture->inspect($this->graph)->getCycles();

        self::assertCount(1, $cycles);
        self::assertSame('B -> C -> B', $cycles[0]->toString());
    }

    public function testFindComplexCyle()
    {
        $vertexA = $this->graph->createVertex('A');
        $vertexB = $this->graph->createVertex('B');
        $vertexC = $this->graph->createVertex('C');
        $vertexD = $this->graph->createVertex('D');

        $vertexA->createEdgeTo($vertexB);
        $vertexA->createEdgeTo($vertexC);
        $vertexB->createEdgeTo($vertexD);
        $vertexB->createEdgeTo($vertexC);
        $vertexD->createEdgeTo($vertexA);

        $cycles = $this->fixture->inspect($this->graph)->getCycles();

        self::assertCount(1, $cycles);
        self::assertSame('A -> B -> D -> A', $cycles[0]->toString());
    }

    public function testFindMultipleCyles()
    {
        $vertexA = $this->graph->createVertex('A');
        $vertexB = $this->graph->createVertex('B');
        $vertexC = $this->graph->createVertex('C');
        $vertexD = $this->graph->createVertex('D');

        $vertexA->createEdgeTo($vertexB);
        $vertexB->createEdgeTo($vertexA);
        $vertexC->createEdgeTo($vertexD);
        $vertexD->createEdgeTo($vertexC);

        $cycles = $this->fixture->inspect($this->graph)->getCycles();

        self::assertCount(2, $cycles);
        self::assertSame('A -> B -> A', $cycles[0]->toString());
        self::assertSame('C -> D -> C', $cycles[1]->toString());
    }

    public function testFindMultipleComplexCyles()
    {
        $vertexA = $this->graph->createVertex('A');
        $vertexB = $this->graph->createVertex('B');
        $vertexC = $this->graph->createVertex('C');
        $vertexD = $this->graph->createVertex('D');
        $vertexF = $this->graph->createVertex('F');
        $vertexG = $this->graph->createVertex('G');
        $vertexH = $this->graph->createVertex('H');
        $vertexI = $this->graph->createVertex('I');

        $vertexA->createEdgeTo($vertexB);
        $vertexA->createEdgeTo($vertexC);
        $vertexB->createEdgeTo($vertexC);
        $vertexB->createEdgeTo($vertexD);
        $vertexD->createEdgeTo($vertexA);
        $vertexF->createEdgeTo($vertexG);
        $vertexF->createEdgeTo($vertexH);
        $vertexG->createEdgeTo($vertexH);
        $vertexG->createEdgeTo($vertexI);
        $vertexI->createEdgeTo($vertexF);

        $cycles = $this->fixture->inspect($this->graph)->getCycles();

        self::assertCount(2, $cycles);
        self::assertSame('A -> B -> D -> A', $cycles[0]->toString());
        self::assertSame('F -> G -> I -> F', $cycles[1]->toString());
    }

    /**
     * @group only
     */
    public function testFindCyleInCycle()
    {
        $vertexA = $this->graph->createVertex('A');
        $vertexB = $this->graph->createVertex('B');
        $vertexC = $this->graph->createVertex('C');
        $vertexD = $this->graph->createVertex('D');
        $vertexE = $this->graph->createVertex('E');
        $vertexF = $this->graph->createVertex('F');
        $vertexG = $this->graph->createVertex('G');
        $vertexH = $this->graph->createVertex('H');
        $vertexI = $this->graph->createVertex('I');
        $vertexJ = $this->graph->createVertex('J');

        $vertexA->createEdgeTo($vertexB);
        $vertexB->createEdgeTo($vertexC);
        $vertexC->createEdgeTo($vertexD);
        $vertexD->createEdgeTo($vertexE);
        $vertexE->createEdgeTo($vertexF);
        $vertexF->createEdgeTo($vertexB);

        $vertexB->createEdgeTo($vertexG);
        $vertexG->createEdgeTo($vertexH);
        $vertexH->createEdgeTo($vertexI);
        $vertexI->createEdgeTo($vertexJ);
        $vertexJ->createEdgeTo($vertexF);

        $cycles = $this->fixture->inspect($this->graph)->getCycles();

        self::assertCount(2, $cycles);
        self::assertSame('B -> C -> D -> E -> F -> B', $cycles[0]->toString());
        self::assertSame('B -> G -> H -> I -> J -> F -> B', $cycles[1]->toString());
    }

    public function testFindParallelCycles()
    {
        $vertexA = $this->graph->createVertex('A');
        $vertexB = $this->graph->createVertex('B');
        $vertexC = $this->graph->createVertex('C');
        $vertexD = $this->graph->createVertex('D');
        $vertexE = $this->graph->createVertex('E');
        $vertexF = $this->graph->createVertex('F');

        $vertexA->createEdgeTo($vertexB);
        $vertexC->createEdgeTo($vertexD);
        $vertexC->createEdgeTo($vertexB);
        $vertexB->createEdgeTo($vertexE);
        $vertexE->createEdgeTo($vertexC);
        $vertexB->createEdgeTo($vertexF);
        $vertexF->createEdgeTo($vertexC);

        $cycles = $this->fixture->inspect($this->graph)->getCycles();

        self::assertCount(2, $cycles);
        self::assertSame('B -> E -> C -> B', $cycles[0]->toString());
        self::assertSame('B -> F -> C -> B', $cycles[1]->toString());
    }

    public function testFindCycleOnCycle()
    {
        $vertexA = $this->graph->createVertex('A');
        $vertexB = $this->graph->createVertex('B');
        $vertexC = $this->graph->createVertex('C');
        $vertexD = $this->graph->createVertex('D');
        $vertexE = $this->graph->createVertex('E');
        $vertexF = $this->graph->createVertex('F');
        $vertexG = $this->graph->createVertex('G');
        $vertexH = $this->graph->createVertex('H');
        $vertexI = $this->graph->createVertex('I');

        $vertexA->createEdgeTo($vertexB);
        $vertexB->createEdgeTo($vertexC);
        $vertexC->createEdgeTo($vertexD);
        $vertexD->createEdgeTo($vertexE);
        $vertexE->createEdgeTo($vertexF);
        $vertexF->createEdgeTo($vertexB);
        $vertexD->createEdgeTo($vertexG);
        $vertexG->createEdgeTo($vertexH);
        $vertexH->createEdgeTo($vertexI);
        $vertexI->createEdgeTo($vertexF);

        $cycles = $this->fixture->inspect($this->graph)->getCycles();

        self::assertCount(2, $cycles);
        self::assertSame('B -> C -> D -> E -> F -> B', $cycles[0]->toString());
        self::assertSame('B -> C -> D -> G -> H -> I -> F -> B', $cycles[1]->toString());
    }

    public function testFindCycledEdges()
    {
        $vertexA = $this->graph->createVertex('A');
        $vertexB = $this->graph->createVertex('B');
        $vertexC = $this->graph->createVertex('C');
        $vertexD = $this->graph->createVertex('D');
        $vertexE = $this->graph->createVertex('E');

        $vertexA->createEdgeTo($vertexB);
        $vertexB->createEdgeTo($vertexA);
        $vertexC->createEdgeTo($vertexE);
        $vertexD->createEdgeTo($vertexE);

        $cycledEdges = $this->fixture->inspect($this->graph)->getCycledEdges();

        self::assertSame(2, $cycledEdges->count());
    }
}
