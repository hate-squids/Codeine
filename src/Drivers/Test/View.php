<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.6.2
     */

    self::setFn ('Root', function ($Call)
    {
        $Call['Renderer'] = 'View.HTML';

        $Call['Output']['Content'] =
            array(
                array(
                    'Type' => 'Heading',
                    'Level' => 1,
                    'Value' => 'Codeine 7 works!'
                ),
                array (
                    'Type'  => 'Heading',
                    'Level' => 2,
                    'Value' => $_SERVER['HTTP_HOST']
                )
            );

        return $Call;
    });