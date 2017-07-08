<?php

/**
 * This file is part of phuria/pipolino package.
 *
 * Copyright (c) 2017 Beniamin Jonatan Šimko
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phuria\Pipolino;

/**
 * @author Beniamin Jonatan Šimko <contact@simko.it>
 */
class Pipolino
{
    /**
     * @var array
     */
    private $stages;

    /**
     * @var callable
     */
    private $defaultStage;

    /**
     * @param array    $stages
     * @param callable $defaultStage
     *
     * @throws InvalidStageException
     */
    public function __construct(array $stages = [], callable $defaultStage = null)
    {
        foreach ($stages as $stage) {
            if (false === is_callable($stage)) {
                throw InvalidStageException::create($stage);
            }
        }

        $this->stages = $stages;
        $this->defaultStage = $defaultStage ?: new DefaultStage();
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
            return call_user_func($this->defaultStage, ...$args);
        }

        $stages = $this->stages;
        $currentStage = array_shift($stages);

        $next = function (...$args) use ($stages) {
            return (new Pipolino($stages, $this->defaultStage))->process(...$args);
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

        return new self($stages, $this->defaultStage);
    }

    /**
     * @param callable $stage
     *
     * @return Pipolino
     */
    public function withDefaultStage(callable $stage)
    {
        return new self($this->stages, $stage);
    }
}
