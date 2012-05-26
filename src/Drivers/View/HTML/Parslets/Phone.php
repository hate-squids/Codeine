<?php

    /* Codeine
     * @author BreathLess
     * @description Exec Parslet 
     * @package Codeine
     * @version 6.0
     */

     self::setFn('Parse', function ($Call)
     {
          foreach ($Call['Parsed'][2] as $Ix => $Match)
          {
              preg_match ('/(\+(\d+)) ?\(?(\d{3})\)? ?(\d{3})[ -]?(\d{2})[ -]?(\d{2})/', $Match, $Parts);
              $TelForm = implode ('', array_slice ($Parts, 2, 6));
              $HumanForm = '+'.$Parts[2].' ('. $Parts[3].') '. $Parts[4].' '. $Parts[5]. ' '. $Parts[6];

              $Call['Output'] = str_replace($Call['Parsed'][0][$Ix], '<a class="tel" href="tel:'.$TelForm.'">'.$HumanForm.'</a>', $Call['Output']);
          }

          return $Call;
     });