<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.4
     */

    setFn('Do', function ($Call)
    {
        $Call = F::Merge(F::loadOptions('Entity.'.$Call['Entity']), $Call); // FIXME
        $Call = F::Hook('beforeCatalog', $Call);

        $Call['Layouts'][] = ['Scope' => $Call['Entity'],'ID' => 'Catalog'];

        $Elements = F::Run('Entity', 'Read', ['Entity' => $Call['Entity'], 'Fields' => [$Call['Key']]]);

        foreach ($Elements as $Element)
            $Keys[] = F::Dot($Element, $Call['Key']);

        $Keys = array_count_values($Keys);

        foreach ($Keys as $Value => $Count)
            $Call['Output']['Content'][]=
                array(
                    'Type' => 'Template',
                    'Scope' => $Call['Entity'],
                    'ID' => 'Catalog/'.$Call['Key'],
                    'Data' =>
                    [
                        'Count' => $Count,
                        'Value' => $Value
                    ]
                );

        $Call = F::Hook('afterCatalog', $Call);

        return $Call;
    });