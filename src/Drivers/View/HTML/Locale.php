<?php

    /* Codeine
     * @author BreathLess
     * @description: 
     * @package Codeine
     * @version 7.2
     */

    self::setFn('Process', function ($Call)
    {
        $Locales = array();

        if (preg_match_all('@<locale>(.*)<\/locale>@SsUu', $Call['Output'], $Pockets))
        {
            $Language = F::Run('System.Interface.Web', 'DetectUALanguage');

            foreach ($Pockets[1] as $IX => $Match)
            {
                list($Asset, $ID) = F::Run('View', 'Asset.Route', array('Value' => $Match));

                $Locales = F::Merge($Locales, F::Run('IO', 'Read',
                    array (
                          'Storage' => 'Locale',
                          'Scope'   => $Asset.'/locale/'.$Language,
                          'Where'   => $ID
                    )));


                $Call['Output'] = str_replace($Pockets[0][$IX], '', $Call['Output']);
            }

            if (preg_match_all('@<l>(.*)<\/l>@SsUu', $Call['Output'], $Pockets))
            {

                foreach (
                    $Pockets[1] as $IX => $Match
                )
                {
                    $Slices   = explode('.', $Match);
                    $szSlices = sizeof($Slices);

                    $TrueMatch = false;

                    for (
                        $ic = $szSlices; $ic > 0; --$ic
                    )
                        if (isset($Locales[$cMatch = implode('.', array_slice($Slices, 0, $ic))]))
                        {
                            $TrueMatch = $cMatch;
                            break;
                        }

                    if ($TrueMatch)
                        $Call['Output'] = str_replace($Pockets[0][$IX], $Locales[$Match], $Call['Output']);
                    else
                        $Call['Output'] = str_replace($Pockets[0][$IX], '<span class="nl">' . $Match . '</span>', $Call['Output']);
                }
            }
        }
        return $Call;
    });
