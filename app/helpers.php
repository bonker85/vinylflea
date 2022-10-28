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

function format_size($bytes)
{
    if ($bytes >= 1073741824)
    {
        $bytes = number_format($bytes / 1073741824) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = number_format($bytes / 1048576) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = number_format($bytes / 1024) . ' KB';
    }
    elseif ($bytes > 1)
    {
        $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
        $bytes = $bytes . ' byte';
    }
    else
    {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function rrmdir(string $directory): bool
{
    array_map(fn (string $file) => is_dir($file) ? rrmdir($file) : unlink($file), glob($directory . '/' . '*'));

    return rmdir($directory);
}

function cdn_url($url, $item)
{
    if (env('CDN_ENABLE') && $item->cdn_status) {
        $url = str_replace([env('APP_URL'), 'storage/'], [env('CDN_HOST'),''], $url) . '?tm=' . $item->cdn_update_time;
    }
    return $url;
}
function thumb_url($url, $item)
{
    if ($item->thumb) {
        $url = thumb_file($url) . '?tm=' .
                $item->thumb_update_time;
    } else {
        $url =  cdn_url($url, $item);
    }
    return $url;
}
function thumb_file($path)
{
    return str_replace('/users/', '/advert_thumbs/',
        str_replace('.' . pathinfo($path, PATHINFO_EXTENSION), '.webp', $path));
}


function send_telegram($method, $data)
{
    $ch = curl_init();
    curl_setopt_array(
        $ch,
        array(
            CURLOPT_URL => 'https://api.telegram.org/bot' . env("TELEGRAM_TOKEN") . '/' . $method,
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => $data,
        )
    );
    curl_exec($ch);
}
?>
