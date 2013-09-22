<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Do', function ($Call)
    {
        if (isset($Call['Current Image']['Thumb']) && !empty($Call['Current Image']['Data']))
        {
            $GImage = new Gmagick();
            $GImage->readimageblob($Call['Current Image']['Data']);
            $GImage->setCompressionQuality($Call['Image']['Quality']);

            if (!isset($Call['Current Image']['Height']))
                $Call['Current Image']['Height'] =
                    ($Call['Current Image']['Width']/$GImage->getimagewidth())*$GImage->getimageheight();

            $GImage->cropthumbnailimage($Call['Current Image']['Width'], $Call['Current Image']['Height']);

            $Call['Current Image']['Data'] = $GImage->getImageBlob();
        }

        return $Call;
    });