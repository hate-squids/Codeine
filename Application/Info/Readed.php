<?php

    $Object = new Object(self::$Name);

    if ($Object->Load(self::$ID))
        Client::$Face->Toggle('Bookmark', self::$Name.':'.self::$ID);

    Page::Add('<icon>Sidebar/Tick</icon> Готово');