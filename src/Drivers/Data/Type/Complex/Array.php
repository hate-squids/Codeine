<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Write', function ($Call)
    {
        return (array) $Call['Value'];
    });

    setFn('Read', function ($Call)
    {
        return (array) $Call['Value'];
    });

    setFn('Where', function ($Call)
    {
        return null;
    });