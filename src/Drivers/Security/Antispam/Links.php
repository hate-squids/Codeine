<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Calculate', function ($Call)
    {
        if (preg_match_all('/href=\=/', $Call['Value'], $Pockets))
            return count($Pockets)*$Call['Antispam']['Link']['Weight'];
        else
            return 0;
    });