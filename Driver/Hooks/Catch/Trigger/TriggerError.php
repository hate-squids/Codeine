<?php

    /* OSWA Codeine
     * @author BreathLess
     * @type Codeine Driver
     * @description: 
     * @package Codeine
     * @subpackage Drivers
     * @version 5.0
     * @date 25.02.11
     * @time 21:44
     */

    self::Fn('Catch', function ($Call)
    {
        return trigger_error($Call['Event']);
    });
