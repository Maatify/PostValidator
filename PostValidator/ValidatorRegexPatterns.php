<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-11-02
 * Time: 3:47 PM
 * https://www.Maatify.dev
 */

namespace Maatify\PostValidator;

abstract class ValidatorRegexPatterns
{
    protected function Patterns(string $typeName): string
    {
        return match ($typeName) {
            'name',
            'name_ar' => '/^[\p{Arabic}a-zA-Z_\-\s\d]*$/iu',
            'name_en' => '/^[a-zA-Z_\-\s]*$/i',
            'username' => '/^[a-zA-Z0-9]*$/i',
            'main_hash' => '/^[A-F0-9]{32}$/',
            'phone' => '/^\d*$/i',
            'phone_full' => '/^\+\d*$/i',
            'year' => '/^(19[0-9][0-9]|2[0-1][0-9][0-9])$/',
            'month' => '/^((0[1-9]|1[0-2]))$/',
            'day' => '/^(0[1-9]|[1-2][0-9]|3[0-1])$/',
            'year_month' => '/^(19[0-9][0-9]|2[0-1][0-9][0-9])-(0[1-9]|1[0-2])$/',
            'date' => '/^(19[0-9][0-9]|2[0-1][0-9][0-9])-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/',
            'datetime' => '/^(19[0-9][0-9]|2[0-1][0-9][0-9])-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/',
            'password' => '/^(?=.*\d)(?=.*[a-zA-Z])[0-9A-Za-z!@#$%+_\-&]{7,70}$/',
            'account_no' => '/^[0-9]{9}$/',
            'egypt_national_id' => '/^[0-9]{14}$/',
            'pin', 'code' => '/^[0-9]{6}$/',
            'app_type' => '/^[1-3]{1}$/',
            'status_id','int' => '/^[0-9]+$/i',
            'float' => '/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/',
            'bool', 'status' => '/^[0-1]{1}$/',
            'device_id' => '/^[a-zA-Z_\-\d]*$/i','token' => '/^[a-zA-Z0-9._\-]+$/',
            'api_key' => '/^[A-Za-z0-9]+$/',
            'slug' => '/^[a-z0-9\-]+$/',
            'letters' => '/^[a-zA-Z]*$/i',
            'small_letters' => '/^[a-z]*$/i',
            'json' => '((\[[^\}]+)?\{s*[^\}\{]{3,}?:.*\}([^\{]+\])?)',
            'search' => '/^[a-zA-Z_\-\s\d]*$/i',
            'col_name' => '/^[a-z_\d]*$/i',
            'stripe_id' => '/^[a-zA-Z\-_\d]*$/i',
            default => '',
        };
    }
}