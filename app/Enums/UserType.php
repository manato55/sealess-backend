<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class UserType extends Enum
{
    const Admin = 0;
    const DepAdmin = 1;
    const OrdinaryUser = 2;

    public static function getValue(string $key)
    {
        if ($key === 'Admin') {
            return self::Admin;
        }

        if ($key === 'DepAdmin') {
            return self::DepAdmin;
        }

        if ($key === 'OrdinaryUser') {
            return self::OrdinaryUser;
        }

        return parent::getValue($key);
    }

}
