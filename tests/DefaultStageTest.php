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

use Phuria\Pipolino\DefaultStage;

/**
 * @author Beniamin Jonatan Šimko <spam@simko.it>
 */
class DefaultStageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @small
     */
    public function testInvoke()
    {
        $stage = new DefaultStage();
        static::assertSame(10, $stage(10, 20, 30));
    }
}
