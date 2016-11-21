<?php

class N2Localization extends N2LocalizationAbstract
{

    static function getLocale() {
        return get_locale();
    }
}
