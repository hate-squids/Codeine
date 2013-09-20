<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn ('Write', function ($Call)
    {
        $Call['Node']['Options'] = F::Live($Call['Node']['Options']);
        foreach ($Call['Value'] as &$Value)
            $Value = array_search($Value, $Call['Node']['Options']);

        return $Call['Value'];
    });

    setFn('Read', function ($Call)
    {
        $Call['Node']['Options'] = F::Live($Call['Node']['Options']);

        if(is_array($Call['Value']))
            foreach ($Call['Value'] as &$Value)
                $Value = $Call['Node']['Options'][$Value];

        return $Call['Value'];
    });

    setFn('Where', function ($Call)
    {
        $Call['Node']['Options'] = F::Live($Call['Node']['Options']);

        foreach ($Call['Value'] as &$Value)
            $Value = array_search($Value, $Call['Node']['Options']);
    });

    setFn('Populate', function ($Call)
    {
        return [array_rand(F::Live($Call['Node']['Options']))];
    });