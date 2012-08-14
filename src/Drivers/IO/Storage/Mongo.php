<?php

   /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.6.2
     */

    self::setFn ('Open', function ($Call)
    {
        $Link = new Mongo('mongodb://'.$Call['Server']);
        $Link = $Link->selectDB($Call['Database']);

        if (isset($Call['Auth']))
            $Link->authentificate($Call['Auth']['Username'], $Call['Auth']['Password']);

        return $Link;
    });

    self::setFn ('Read', function ($Call)
    {
        F::Counter('IO.Mongo');
        F::Counter('IO.Mongo.Reads');

        foreach ($Call['Where'] as $Key => &$Value) // FIXME Повысить уровень абстракции
        {
            if (isset($Call['Nodes'][$Key]['Type']))
            {
                if (is_array($Value))
                    foreach ($Value as &$cValue)
                        $cValue = F::Run('Data.Type.'.$Call['Nodes'][$Key]['Type'], 'Read', array('Value' => $cValue, 'Purpose' => 'Where'));
                else
                    $Value = F::Run('Data.Type.'.$Call['Nodes'][$Key]['Type'], 'Read', array('Value' => $Value, 'Purpose' => 'Where'));
            }

        }

        unset($Value, $Key);

        if (isset($Call['Where']))
            $Cursor = $Call['Link']->$Call['Scope']->find($Call['Where']);
        else
            $Cursor = $Call['Link']->$Call['Scope']->find();

        if (isset($Call['Fields']))
        {
            $Fields = array();
            foreach ($Call['Fields'] as $Field)
                $Fields[$Field] = true;

            $Cursor->fields($Fields);
        }

        if (isset($Call['Sort']))
            foreach($Call['Sort'] as $Key => $Direction)
                $Cursor->sort(array($Key => ($Direction == SORT_ASC? 1: -1)));

        if (isset($Call['Limit']))
            $Cursor->limit($Call['Limit']['To']-$Call['Limit']['From'])->skip($Call['Limit']['From']);

        if ($Cursor->count()>0)
            foreach ($Cursor as $cCursor)
            {
                unset($cCursor['_id']);
                $Data[] = $cCursor;
            }
        else
            $Data = null;

        return $Data;
    });

    self::setFn ('Write', function ($Call)
    {
        F::Counter('IO.Mongo');
        F::Counter('IO.Mongo.Writes');

        foreach ($Call['Where'] as $Key => &$Value) // FIXME Повысить уровень абстракции
        {
            if (isset($Call['Nodes'][$Key]['Type']))
            {
                if (is_array($Value))
                    foreach ($Value as &$cValue)
                        $cValue = F::Run('Data.Type.'.$Call['Nodes'][$Key]['Type'], 'Read', array('Value' => $cValue));
                else
                    $Value = F::Run('Data.Type.'.$Call['Nodes'][$Key]['Type'], 'Read', array('Value' => $Value));
            }
        }

        unset($Value, $Key);

        if (null === $Call['Data'])
        {
            if (isset($Call['Where']))
                return $Call['Link']->$Call['Scope']->remove ($Call['Where']);
            else
                return $Call['Link']->$Call['Scope']->remove ();
        }
        else
        {
            $Data = array();

            foreach ($Call['Data'] as $Key => $Value)
                $Data = F::Dot($Data, $Key, $Value);

            //if (isset($Call['Current']))
            $Data = F::Merge($Call['Current'], $Data);

            if (isset($Call['Where']))
                $Call['Link']->$Call['Scope']->update($Call['Where'], $Data) or F::Hook('IO.Mongo.Update.Failed', $Call);
            else
                $Call['Link']->$Call['Scope']->insert ($Data);

            return $Data;
        }
    });

    self::setFn ('Close', function ($Call)
    {
        return true;
    });

    self::setFn ('Execute', function ($Call)
    {
        return $Call['Link']->execute($Call['Command']);
    });