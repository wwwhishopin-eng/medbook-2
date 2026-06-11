<?php

namespace App\Helpers;

class JalaliDate
{
    private static $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    private static $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
    private static $j_month_names = [
        1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد',
        4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور',
        7 => 'مهر', 8 => 'آبان', 9 => 'آذر',
        10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
    ];
    private static $j_day_names = [
        0 => 'یکشنبه', 1 => 'دوشنبه', 2 => 'سه‌شنبه',
        3 => 'چهارشنبه', 4 => 'پنج‌شنبه', 5 => 'جمعه', 6 => 'شنبه'
    ];
    private static $j_day_names_short = [
        0 => 'ی', 1 => 'د', 2 => 'س', 3 => 'چ', 4 => 'پ', 5 => 'ج', 6 => 'ش'
    ];

    private static function isLeapYear($year)
    {
        $a = [1, 5, 9, 13, 17, 22, 26, 30];
        $b = $year % 33;
        return in_array($b, $a);
    }

    public static function toJalali($gy, $gm, $gd)
    {
        $g_day_no = 365 * ($gy - 1) + (int)(($gy - 1) / 4) - (int)(($gy - 1) / 100) + (int)(($gy - 1) / 400);

        for ($i = 0; $i < $gm - 1; $i++) {
            $g_day_no += self::$g_days_in_month[$i];
        }

        if ($gm > 2 && ($gy % 400 == 0 || ($gy % 100 != 0 && $gy % 4 == 0))) {
            $g_day_no++;
        }

        $g_day_no += $gd - 1;

        $j_day_no = $g_day_no - 79;

        $j_np = (int)($j_day_no / 12053);
        $j_day_no %= 12053;

        $jy = 979 + 33 * $j_np + 4 * (int)($j_day_no / 1461);
        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += (int)(($j_day_no - 1) / 365);
            $j_day_no = ($j_day_no - 1) % 365;
        }

        for ($i = 0; $i < 11 && $j_day_no >= self::$j_days_in_month[$i]; $i++) {
            $j_day_no -= self::$j_days_in_month[$i];
        }

        $jm = $i + 1;
        $jd = $j_day_no + 1;

        return [$jy, $jm, $jd];
    }

    public static function toGregorian($jy, $jm, $jd)
    {
        $jy = (int)$jy - 979;
        $jm = (int)$jm - 1;
        $jd = (int)$jd - 1;

        $j_day_no = 365 * $jy + (int)($jy / 33) * 8 + (int)(($jy % 33 + 3) / 4);

        for ($i = 0; $i < $jm; $i++) {
            $j_day_no += self::$j_days_in_month[$i];
        }

        $j_day_no += $jd;

        $g_day_no = $j_day_no + 79;

        $gy = 1600 + 400 * (int)($g_day_no / 146097);
        $g_day_no %= 146097;

        $leap = true;
        if ($g_day_no >= 36525) {
            $g_day_no--;
            $gy += 100 * (int)($g_day_no / 36524);
            $g_day_no %= 36524;
            if ($g_day_no >= 365) {
                $g_day_no++;
            } else {
                $leap = false;
            }
        }

        $gy += 4 * (int)($g_day_no / 1461);
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;
            $g_day_no--;
            $gy += (int)($g_day_no / 365);
            $g_day_no = $g_day_no % 365;
        }

        $g_day_no++;

        for ($i = 0; $i < 11 && $g_day_no > self::$g_days_in_month[$i] + ($i == 1 && $leap ? 1 : 0); $i++) {
            $g_day_no -= self::$g_days_in_month[$i] + ($i == 1 && $leap ? 1 : 0);
        }

        $gm = $i + 1;
        $gd = $g_day_no;

        return [$gy, $gm, $gd];
    }

    public static function format($date, $format = 'Y/m/d')
    {
        if (!$date) return '';

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        $j = self::toJalali($date->year, $date->month, $date->day);
        $jy = $j[0];
        $jm = $j[1];
        $jd = $j[2];

        $dayOfWeek = $date->dayOfWeek;

        $output = $format;
        $output = str_replace('Y', (string)$jy, $output);
        $output = str_replace('m', str_pad($jm, 2, '0', STR_PAD_LEFT), $output);
        $output = str_replace('d', str_pad($jd, 2, '0', STR_PAD_LEFT), $output);
        $output = str_replace('n', (string)$jm, $output);
        $output = str_replace('j', (string)$jd, $output);
        $output = str_replace('F', self::$j_month_names[$jm], $output);
        $output = str_replace('l', self::$j_day_names[$dayOfWeek], $output);
        $output = str_replace('D', self::$j_day_names_short[$dayOfWeek], $output);
        $output = str_replace('H', $date->format('H'), $output);
        $output = str_replace('i', $date->format('i'), $output);
        $output = str_replace('s', $date->format('s'), $output);

        return Persian::digits($output);
    }

    public static function parse($jalaliDate, $format = 'Y/m/d')
    {
        $jalaliDate = Persian::toWestern($jalaliDate);
        $parts = preg_split('/\/|-/', $jalaliDate);

        if (count($parts) !== 3) {
            return null;
        }

        $jy = (int)$parts[0];
        $jm = (int)$parts[1];
        $jd = (int)$parts[2];

        $g = self::toGregorian($jy, $jm, $jd);

        return \Carbon\Carbon::create($g[0], $g[1], $g[2], 0, 0, 0);
    }

    public static function now()
    {
        return self::format(now());
    }

    public static function createFromFormat($format, $jalaliDate)
    {
        return self::parse($jalaliDate, $format);
    }

    public static function daysInMonth($jy, $jm)
    {
        if ($jm <= 6) return 31;
        if ($jm <= 11) return 30;
        return self::isLeapYear($jy) ? 30 : 29;
    }

    public static function monthName($month)
    {
        return self::$j_month_names[(int)$month] ?? '';
    }

    public static function dayName($dayOfWeek)
    {
        return self::$j_day_names[$dayOfWeek] ?? '';
    }

    public static function getMonths()
    {
        return self::$j_month_names;
    }
}
