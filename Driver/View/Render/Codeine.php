<?php

    /* OSWA Codeine
     * @author BreathLess
     * @type Codeine Driver
     * @description: D8 Port
     * @package Codeine
     * @subpackage Drivers
     * @version 5.0
     * @date 16.11.10
     * @time 3:48
     */

    self::Fn('Do', function ($Call)
    {
        // Определить тип данных

        // Контроллер отдаёт набор UI компонентов

        $Layout = Data::Read(
                array(
                    'Point' => 'Layout',
                    'Where' =>
                        array(
                            'ID'=>'Main')));

        $Output = array();

        // Обработка контролов
        
        if (is_array($Call['Input']['Items']))
            foreach ($Call['Input']['Items'] as $ID => $Item)
            {
                $Output[$ID] = Code::Run(
                    array('F'=>'View/UI/Codeine/'.$Item['UI'].'::Make',
                         'D' => $Item ['UI'],
                         'Item'=> Core::Any($Item))
                );
            }

        $Output = implode('',$Output);

        // Профьюзить
        // Постпроцессинг

        $Output = str_replace('<content/>',$Output, $Layout);

        $Processors = array('HTML/Media', 'HTML/I18N'); // FIXME!!

        foreach ($Processors as $Processor)
            $Output = Code::Run(
                array('F'=>'View/Processors/'.$Processor.'::Process',
                      'Input'=> $Output)
            );

        // Вернуть



        return $Output;
    });