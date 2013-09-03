<?php

/**
 * @author BreathLess
 * @date 27.28.11
 * @time 5:17
 */


    include 'Codeine/Core.php';

    try
    {
        if (file_exists(Root.'/locks/down') && !isset($_COOKIE['Magic']))
            throw new Exception('down');

        $Call = F::Bootstrap
        ([
            'Path' => [Root],
            'Service' => 'System.Interface.Web',
            'Method' => 'Do',
            'Call' =>
            [
                'Service' => 'Code.Flow.Front',
                'Method'  => 'Run'
            ]
        ]);
    }
    catch (Exception $e)
    {
        switch ($_SERVER['Environment'])
        {
            case 'Production':
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                header('Status: 503 Service Temporarily Unavailable');

                if (file_exists(Root.'/down.html'))
                    readfile(Root.'/down.html');
                else
                    readfile(Codeine.'/down.html');
            break;

            default:
                d(__FILE__, __LINE__, $e); ;
            break;
        }
    }



