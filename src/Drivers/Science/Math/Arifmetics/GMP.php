<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Add', function ($Call)
    {
        return gmp_add($Call['A'],$Call['B']);
    });

    setFn('Substract', function ($Call)
    {
        return gmp_sub($Call['A'],$Call['B']);
    });

    setFn('Multiply', function ($Call)
    {
        return gmp_mul($Call['A'],$Call['B']);
    });

    setFn('Divide', function ($Call)
    {
        return gmp_div($Call['A'],$Call['B']);
    });