<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Process', function ($Call)
    {
        $Call['Value'] = preg_replace('/(^|\s)"(\S)/', '$1«$2', $Call['Value']);
        $Call['Value'] = preg_replace('/(\S)"([ .,?!])/', '$1»$2', $Call['Value']);

        return $Call;
     });