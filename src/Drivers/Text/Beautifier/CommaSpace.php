<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.2
     */

    self::setFn('Process', function ($Call)
    {
        $Call['Value'] = preg_replace('/,(\w+)/i', ', \1', $Call['Value']);
        return $Call;
     });