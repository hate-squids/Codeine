<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.6.2
     */

    self::setFn('And', function ($Call)
    {
        return $Call['A'] && $Call['B'];
    });

    self::setFn('Or', function ($Call)
    {
        return $Call['A'] || $Call['B'];
    });

    self::setFn('Xor', function ($Call)
    {
        return $Call['A'] xor $Call['B'];
    });

    self::setFn('Not', function ($Call)
    {
        return !$Call['A'] && $Call['B'];
    });