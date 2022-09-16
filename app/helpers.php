<?php
if (!function_exists('translate_url')) {
    function translate_url($name) {
        $ru = explode('-', "А-а-Б-б-В-в-Ґ-ґ-Г-г-Д-д-Е-е-Ё-ё-Є-є-Ж-ж-З-з-И-и-І-і-Ї-ї-Й-й-К-к-Л-л-М-м-Н-н-О-о-П-п-Р-р-С-с-Т-т-У-у-Ф-ф-Х-х-Ц-ц-Ч-ч-Ш-ш-Щ-щ-Ъ-ъ-Ы-ы-Ь-ь-Э-э-Ю-ю-Я-я");
        $en = explode('-', "A-a-B-b-V-v-G-g-G-g-D-d-E-e-E-e-E-e-ZH-zh-Z-z-I-i-I-i-I-i-J-j-K-k-L-l-M-m-N-n-O-o-P-p-R-r-S-s-T-t-U-u-F-f-H-h-TS-ts-CH-ch-SH-sh-SCH-sch---Y-y---E-e-YU-yu-YA-ya");

        $res = str_replace($ru, $en, $name);
        $res = preg_replace("/[\s]+/ui", '-', $res);
        $res = strtolower(preg_replace("/[^0-9a-zа-я\-]+/ui", '', $res));

        return $res;
    }
}
if (!function_exists('make_directory')) {
    function make_directory($path, $mode = 0777, $recursive = false, $force = false)
    {
        if (!file_exists($path)) {
            if ($force)
            {
                return @mkdir($path, $mode, $recursive);
            }
            else
            {
                return mkdir($path, $mode, $recursive);
            }
        }
        return true;
    }
}

?>
