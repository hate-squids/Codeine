<?php

    /* Codeine
     * @author BreathLess
     * @description: Standart SHA1
     * @package Codeine
     * @version 7.1
     * @date 22.11.10
     * @time 4:41
     */

    self::setFn('Get', function ($Call)
    {
        return sha1($Call['Value']);
    });

