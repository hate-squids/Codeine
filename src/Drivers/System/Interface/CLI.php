<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.1
     */

    self::setFn ('Run', function ($Call)
    {
        $Call = F::Run ($Call['Service'], $Call['Method'], $Call);

        $Call['Renderer'] = 'View.Render.Striptags';

        $Call = F::Run ('Engine.View', 'Render', $Call);

        echo $Call['Output'];

        return $Call;
    });