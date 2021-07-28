<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class FiscalYear extends Enum
{
    const StartedYearCalender = 2019;
    const yearBeforeNewEra = 2018;

    public static function getJapCalender(string $key)
    {
        $month = substr($key, -2);
        $year = substr($key, 0,4);
        $monthFirstDigit = substr($month, 0,1);
        if($monthFirstDigit == 0) {
            $monthLastDigit = substr($month, -1);
            if($monthLastDigit >= 4) {
                $japCalender =  $year - self::StartedYearCalender + 1;
            } else {
                $japCalender =  $year - self::StartedYearCalender;
            }
        } else {
            $japCalender =  $year - self::StartedYearCalender + 1;
        }

        return $japCalender;
    }

    public static function getYearFromJapYear(string $key)
    {
        // $startYear = $key+self::yearBeforeNewEra;
        // $endYear = $startYear-1;

    }
}
