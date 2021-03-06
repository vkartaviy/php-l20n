<?php

namespace L20n\Platform;

/**
 * Class Platform\HourGlobal
 * @package L20n
 */
class HourGlobal extends GlobalBase
{
    /**
     * @return mixed
     */
    protected function _get()
    {
        /** @var \DateTime $time */
        $time = new \DateTime();

        return (int)$time->format('G');
    }
}
