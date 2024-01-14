<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-11-02
 * Time: 3:00 PM
 * https://www.Maatify.dev
 */

namespace Maatify\PostValidator;

class ValidatorConstants
{
    private static ValidatorConstants $instance;

    public static function obj(): self
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    const ValidatorRequire = 'Required';
    const ValidatorRequireAcceptEmpty = 'RequireAcceptEmpty';
    const ValidatorOptional = 'Optional';

    const TypeEmail = 'email';
    const TypeIP = 'ip';
    const TypeMobileEgypt = 'mobile_egypt';
    const TypeName = 'name';
    const TypeNameEn = 'name_en';
    const TypeNameAr = 'name_ar';
    const TypeUsername = 'username';
    const TypeMainHash = 'main_hash';
    const TypePhone = 'phone';
    const TypePhoneFull = 'phone_full';
    const TypeYear = 'year';
    const TypeMonth = 'month';
    const TypeDay = 'day';
    const TypeYearMonth = 'year_month';
    const TypeDate = 'date';
    const TypeDateTime = 'datetime';
    const TypePassword = 'password';
    const TypeAccountNo = 'account_no';
    const TypePin = 'pin';
    const TypeCode = 'code';
    const TypeAppType = 'app_type';
    const TypeInt = 'int';
    const TypeFloat = 'float';
    const TypeBool = 'bool';
    const TypeDeviceId = 'device_id';
    const Status = 'status';
}