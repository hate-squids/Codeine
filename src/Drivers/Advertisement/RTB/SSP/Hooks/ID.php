<?php
    
    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */
    
    setFn('Do', function ($Call)
    {
        $ID = F::Run('Security.UID', 'Get', $Call, ['Mode' => F::Dot($Call, 'RTB.Request.ID.Mode')]);
        $Call = F::Dot($Call, 'RTB.DSP.Request.id', $ID);
        
        F::Log('RTB Request ID generated: *'.$ID.'*', LOG_INFO);
        
        return $Call;
    });
