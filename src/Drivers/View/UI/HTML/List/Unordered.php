<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 6.0
     */

     self::Fn('Make', function ($Call)
     {
         $Output = '<ul'.(isset($Call['Class.List'])? ' class = "'.$Call['Class.List'].'"': '').'>';

         foreach($Call['Value'] as $Element)
             $Output.= '<li>'.$Element.'</li>';

         $Output .= '</ul>';

         return $Output;
     });