<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.0
     */

    setFn('Parse', function ($Call)
    {
        foreach ($Call['Parsed'][2] as $Ix => $Match)
        {
            if (preg_match('@^(.+)\:(.+)\:(.+)$@SsUu', $Match, $Slices))
            {
                list(,$Entity, $ID, $Key) = $Slices;

                    $Element = F::Run('Entity', 'Read',
                        [
                          'Entity' => $Entity,
                          'Where'  => $ID,
                          'One' => true
                        ]);

                    if (!empty($Element))
                        $Call['Output'] =
                            str_replace($Call['Parsed'][0][$Ix], F::Dot($Element, $Key),$Call['Output']);
                    else
                        $Call['Output'] =
                            str_replace($Call['Parsed'][0][$Ix], '' . $ID, $Call['Output']);
            }
            else
                $Call['Output'] = str_replace($Call['Parsed'][0][$Ix], '' . $Match, $Call['Output']);

        }

        return $Call;
     });