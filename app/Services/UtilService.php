<?php

namespace App\Services;

use Carbon\Carbon;

class UtilService
{
    public static function isValidDate($date): bool
    {
        return (bool) strtotime($date);
    }

    public static function convertDate($date, $from): Carbon
    {
        return Carbon::createFromFormat($from, $date);
    }

    public static function convertDateToFormat($date, $from, $to): string
    {
        return self::convertDate($date, $from)->format($to);
    }

    public static function stringDecrement($string)
    {
        $len = strlen($string);
        if ($len == 1) {
            if (strcasecmp($string, "A") == 0) {
                return "A";
            }
            return chr(ord($string) - 1);
        } else {
            $s = substr($string, -1);
            if (strcasecmp($s, "A") == 0) {
                $s = substr($string, -2, 1);
                if (strcasecmp($s, "A") == 0) {
                    $s = "Z";
                } else {
                    $s = chr(ord($s) - 1);
                }
                $output = substr($string, 0, $len - 2) . $s;
                if (strlen($output) != $len && $string != "AA") {
                    $output .= "Z";
                }
                return $output;
            } else {
                $output = substr($string, 0, $len - 1) . chr(ord($s) - 1);
                return $output;
            }
        }
    }

    public static function alphanumericGenerator($strength = 6, $withLower = true): string
    {
        $permittedChars = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($withLower) {
            $permittedChars .= 'abcdefghjkmnpqrstuvwxyz';
        }

        $input_length = strlen($permittedChars);
        $random_string = '';
        for ($i = 0; $i < $strength; $i++) {
            $random_character = $permittedChars[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return (string) $random_string;
    }

    public static function calculatePercentageDifference(float $old, float $new): float
    {
        $difference = 100 - (abs(($new - $old) / $old) * 100);

        return (float) $difference;
    }
}
