<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn ('Page', function ($Call)
    {
        $Call['HTTP']['Headers']['HTTP/1.0'] = '500 Internal Server Error';

        $Call['Page']['Title'] = '500';
        $Call['Page']['Description'] = 'TODO';
        $Call['Page']['Keywords'] = array ('TODO');

        $Call['Output']['Content'] = [[
                                        'Type'  => 'Template',
                                        'Scope' => 'Errors',
                                        'Value' => '500',
                                        'Data' => []
                                    ]];
        return $Call;
     });