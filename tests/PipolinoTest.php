<?php

/**
 * This file is part of phuria/pipolino package.
 *
 * Copyright (c) 2017 Beniamin Jonatan Šimko
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phuria\Pipolino\Tests;

use Phuria\Pipolino\InvalidStageException;
use Phuria\Pipolino\Pipolino;

/**
 * @author Beniamin Jonatan Šimko <contact@simko.it>
 */
class PipolinoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @small
     */
    public function testEmpty()
    {
        static::assertSame('OK', (new Pipolino())->process('OK'));
    }

    /**
     * @small
     */
    public function testOneStage()
    {
        $pipolino = (new Pipolino())->addStage(function (callable $next, $i) {
            return $next($i * 2);
        });

        static::assertSame(10, $pipolino->process(5));
        static::assertSame(20, $pipolino->process(10));
    }

    /**
     * @small
     */
    public function testMultipleStages()
    {
        $pipolino = (new Pipolino())
            ->addStage(function (callable $next, $i) {
                return $next($i + 10);
            })
            ->addStage(function (callable $next, $i) {
                return $next($i + 50);
            });

        static::assertSame(100, $pipolino->process(40));

        $pipolino = $pipolino->addStage(function () {
            return 123;
        });

        static::assertSame(123, $pipolino->process(40));
    }

    /**
     * @small
     */
    public function testMultipleArgs()
    {
        $pipolino = (new Pipolino())
            ->addStage(function (callable $next, $a, $b) {
                return $next($a * $b, $b);
            })
            ->addStage(function (callable $next, $a, $b) {
                return $next($a + $b);
            });

        static::assertSame(10 * 2 + 2, $pipolino->process(10, 2));
    }

    /**
     * @small
     */
    public function testPipolinoInception()
    {
        $pipolino = (new Pipolino())->addStage(function (callable $next, $a) {
            return $next($a * 10);
        });

        $pipolino = $pipolino->addStage($pipolino);

        static::assertSame(10 * 10 * 10, $pipolino->process(10));
    }

    /**
     * @small
     */
    public function testCustomDefaultStage()
    {
        $pipolino = (new Pipolino())->addStage(function (callable $next, $a, $b) {
            return $next($a, $b);
        })->withDefaultStage(function ($a, $b) {
            return $a + $b;
        });

        static::assertSame(5, $pipolino->process(2, 3, 5));
    }

    /**
     * @small
     */
    public function testInvalidStageString()
    {
        $this->expectException(InvalidStageException::class);
        new Pipolino(['test']);
    }

    /**
     * @small
     */
    public function testInvalidStageArray()
    {
        $this->expectException(InvalidStageException::class);
        new Pipolino([['a', 'b', 'c']]);
    }

    /**
     * @small
     */
    public function testInvalidStageObject()
    {
        $this->expectException(InvalidStageException::class);
        new Pipolino([new \DateTime()]);
    }
}
