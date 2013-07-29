<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Age', function ($Call)
    {
        if (isset($Call['Data']['DOB']))
            return floor((time()-$Call['Data']['DOB'])/(86400*365));
        else
            return 0;
    });