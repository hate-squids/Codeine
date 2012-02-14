<?php

    /* Codeine
     * @author BreathLess
     * @description HTML Textfield Driver 
     * @package Codeine
     * @version 7.1
     */

     self::setFn('Make', function ($Call)
     {
         $Options = array();

         foreach ($Call['Value'] as $Option)
             $Options[] = '<option>'.$Option.'</option>';

         $Call['Value'] = implode('', $Options);

         return F::Run ('Engine.Template', 'LoadParsed', $Call,
                        array(
                             'Scope' => 'UI',
                             'ID'    => 'HTML/Form/Select',
                             'Data'  => $Call
                        ));
     });