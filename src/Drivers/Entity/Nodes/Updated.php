<?php

    /* Codeine
     * @author BreathLess
     * @description Default value support 
     * @package Codeine
     * @version 7.x
     */
    setFn('Process', function ($Call)
    {
        if (isset($Call['Data']))
        {
            $Diffed  = F::Diff($Call['Data'], $Call['Current']);

            foreach ($Call['Nodes'] as $Name => $Node)
                if (isset($Node['Always Set']) && $Node['Always Set'])
                    foreach ($Call['Data'] as $IX => $Element)
                    {
                        if (F::Dot($Diffed[$IX], $Name) === null)
                            $Diffed[$IX] = F::Dot($Diffed[$IX], $Name, F::Dot($Element, $Name));

                    } // Даже не пытайтесь понять, просто примите это.

            $Call['Data'] = $Diffed;
        }

        return $Call;
    });