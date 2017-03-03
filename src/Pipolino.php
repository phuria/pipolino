<?php

namespace Phuria\Pipolino;

/**
 * This file is part of phuria/pipolino package.
 *
 * Copyright (c) 2017 Beniamin Jonatan Šimko
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Beniamin Jonatan Šimko <spam@simko.it>
 */
class Pipolino
{
    /**
     * @var array
     */
    private $stages;

    /**
     * @param array $stages
     */
    public function __construct(array $stages = [])
    {
        $this->stages = $stages;
    }

    /**
     * @param callable $next
     * @param array    ...$args
     *
     * @return mixed
     */
    public function __invoke(callable $next, ...$args)
    {
        return $this->process(...$args);
    }

    /**
     * @param array ...$args
     *
     * @return mixed
     */
    public function process(...$args)
    {
        if (0 === count($this->stages)) {
            return reset($args);
        }

        $stages = $this->stages;
        $currentStage = array_shift($stages);
        $next = function (...$args) use ($stages) {
            return (new Pipolino($stages))->process(...$args);
        };

        return call_user_func($currentStage, $next, ...$args);
    }

    /**
     * @param callable $stage
     *
     * @return Pipolino
     */
    public function addStage(callable $stage)
    {
        $stages = $this->stages;
        $stages[] = $stage;

        return new self($stages);
    }
}