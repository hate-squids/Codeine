<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Do', function ($Call)
    {
        $Sentences = preg_split('/[.!?;]+/', strip_tags($Call['Value']));

        if (count($Sentences) > $Call['Sentences'] && !empty($Sentences[$Call['Sentences']]))
            $Cutted = mb_substr($Call['Value'], 0, mb_strpos($Call['Value'], $Sentences[$Call['Sentences']])-1);
        else
            $Cutted = $Call['Value'];

        return $Cutted;
    });