<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.4.5
     */

    self::setFn('Store', function ($Call)
    {
        $Call['BackURL'] = $_SERVER['HTTP_REFERER'];
        return $Call;
    });

    self::setFn('Restore', function ($Call)
    {
        $Call = F::Run('System.Interface.Web', 'Redirect', $Call, array('Location' => $Call['Request']['BackURL']));
        return $Call;
    });