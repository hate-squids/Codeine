<?php
    
    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */
    
    setFn('Do', function ($Call)
    {
        $DayStart =  new DateTime ('midnight today');
        $DayFinish= new DateTIme ('midnight tomorrow');

        return  [
                    $Call['Selector']['Day']['Key'] =>
                    [
                        '$gt' => $DayStart->getTimestamp() + ($Call['Selector']['Day']['Increment']*86400),
                        '$lt' => $DayFinish->getTimestamp() + ($Call['Selector']['Day']['Increment']*86400)
                    ]
                ];
    });