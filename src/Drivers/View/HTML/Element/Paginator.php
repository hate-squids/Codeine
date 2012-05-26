<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.2
     */

    self::setFn('Make', function ($Call)
    {
        $Output = '';

        if ($Call['Page']> 1)
            $Output.= F::Run('View', 'LoadParsed',
                array('Scope' => 'Default',
                      'ID' => 'UI/HTML/Paginator/Prev',
                      'Data' =>
                        array(
                            'Num' => $Call['Page']-1,
                            'URL' => $Call['PageURL'])));

        for ($ic = 1; $ic <= $Call['PageCount']; $ic++)
            $Output.= F::Run('View', 'LoadParsed',
                array('Scope' => 'Default',
                      'ID' => ($ic == $Call['Page']? 'UI/HTML/Paginator/Current': 'UI/HTML/Paginator/Page'),
                      'Data' =>
                        array(
                            'Num' => $ic,
                            'URL' => $Call['PageURL'])));

        if ($Call['Page']< $Call['PageCount'])
            $Output.= F::Run('View', 'LoadParsed',
                array('Scope' => 'Default',
                      'ID' => 'UI/HTML/Paginator/Next',
                      'Data' =>
                        array(
                            'Num' => $Call['Page']+1,
                            'URL' => $Call['PageURL'])));

        return F::Run('View', 'LoadParsed', array('Scope' => 'Default', 'ID' => 'UI/HTML/Paginator', 'Data' => array($Call, 'Pages' => $Output)));
     });