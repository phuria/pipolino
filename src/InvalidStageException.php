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
class InvalidStageException extends \RuntimeException
{
    /**
     * @param mixed $stage
     *
     * @return InvalidStageException
     */
    public static function create($stage)
    {
        if (is_object($stage)) {
            return new self(sprintf(
                'Object [%s] is not valid stage. Please check __invoke() implementation.',
                get_class($stage)
            ));
        }

        return new self(sprintf('Invalid stage implementation detected. Probably stage is not callable.'));
    }
}
