<?php
echo get_current_user();
exit;
$time_zone = ini_get('date.timezone');
echo empty($time_zone);
echo  date_default_timezone_get();
echo gmdate(
    'D, d M Y H:i:s \G\M\T',
    strtotime('Sun, 10 Feb 2013 03:06:54 GMT') + 30
);

