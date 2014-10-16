<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Query', function ($Call)
    {
        if (isset($Call['Scope']))
            $Call['Scope'] = $Call['Entity'].'/'.$Call['Scope'];
        else
            $Call['Scope'] = $Call['Entity'];

        $Call['Query'] = preg_split('/\s/', mb_strtolower($Call['Query']));
        $Results = [];

        foreach($Call['Query'] as $Keyword)
        {
            $KeywordResults = F::Run('Entity', 'Read',
                [
                    'Entity' => $Call['Entity'],
                    'Where' =>
                    [
                        'Keywords' => $Keyword // FIXME SOON
                    ]
                ]);

            if (is_array($KeywordResults))
                $Results = array_merge($Results, $KeywordResults);
        }

        $SERP = [];

        if (empty($Results))
            $SERP[$Call['Scope']] =
                [
                    'Type'  => 'Template',
                    'Scope' => $Call['Scope'],
                    'ID'    => 'Empty'
                ];
        else
            foreach ($Results as &$Result)
            {
                $Result['From'] = $Call['HTTP']['Host'];
                $Result['URL']  = $Call['HTTP']['Proto'].$Call['HTTP']['Host'].'/'.strtolower($Call['Entity']).'/'.$Result['ID'];
                $SERP[$Result['URL']] =
                    [
                        'Type'  => 'Template',
                        'Scope' => $Call['Scope'].'/Show',
                        'ID'    => 'Search',
                        'Data'  => $Result
                    ];

            }

        return ['SERP' => $SERP, 'Meta' => ['Total' => count($Results)]];
    });