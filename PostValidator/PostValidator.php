<?php

namespace Maatify\PostValidator;

use \App\Assist\RegexPatterns;
use libphonenumber\NumberParseException;
use Maatify\Json\Json;
use Maatify\Logger\Logger;

class PostValidator extends ValidatorRegexPatterns
{
    protected static int|string $line;
    private static self $instance;
    private array $types = [
        'email'  => 'email',
        'phone'  => 'phone',
        'name'   => 'name',
        'date'   => 'date',
        'string' => 'string',
        'int'    => 'int',
        'pin'    => 'pin',
    ];
    private RegexPatterns $regex_patterns;

    /**
     * @property ?string      $email
     * @property-read ?string $phone
     * @property-read ?string $name
     * @property-read ?string $date
     * @property-read ?string $string
     * @property ?string      $int
     * @property ?string      $float
     */

    public static function obj(): self
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }
        self::$line = debug_backtrace()[0]['line'];

        return self::$instance;
    }

    public function __construct()
    {
        $this->regex_patterns = RegexPatterns::obj();
    }

    public function Require(string $name, string $type = 'string', string $more_info = ''): string
    {
        if (empty($_POST)) {
            Json::MissingMethod();
        }

        if (empty($_POST[$name])) {
            Json::Missing($name, $more_info, self::$line);

            return '';
        }

        if (is_array($_POST[$name])) {
            Json::Invalid($name, $more_info, self::$line);

            return '';
        }

        return $this->HandlePostType($name, $type, $more_info);
    }

    public function RequireAcceptEmpty(string $name, string $type = 'string', string $more_info = ''): string
    {
        if (empty($_POST)) {
            Json::MissingMethod();
        }

        if (! isset($_POST[$name])) {
            Json::Missing($name, $more_info, self::$line);

            return '';
        }

        if (is_array($_POST[$name])) {
            Json::Invalid($name, $more_info, self::$line);

            return '';
        }

        return $this->HandlePostType($name, $type, $more_info);
    }

    public function Optional(string $name, string $type = 'string', string $more_info = ''): string
    {
        if (! empty($_POST) && ! empty($_POST[$name]) && ! is_array($_POST[$name])) {
            return $this->HandlePostType($name, $type, $more_info);
        } else {
            if (in_array($type, ['page', 'limit'])) {
                if (empty($_POST[$name]) || ! is_numeric($_POST[$name])) {
                    return match ($type) {
                        'page' => 1,
                        'limit' => 25,
                    };
                }
            }
        }

        return '';
    }

    private function EmailValidation(string $email, string $name, string $more_info = ''): string
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || ! HostEmailValidation::HostEmailValidation($email)) {
            Json::Invalid($name, $more_info, self::$line);

            return '';
        }

        return $email;
    }


    private static function IPValidation(string $ip, string $name, string $more_info = ''): string
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            Json::Invalid($name, $more_info, self::$line);

            return '';
        }

        return $ip;
    }

    private function ClearInput($data): string
    {
        $data = trim($data);
        $data = stripslashes($data);

        return htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE);
    }

    public function __get(string $name)
    {
        return $this->types[$name] ?? null;
    }

    public function PasswordStrengthCheck($password, $min_len = 8, $max_len = 70): bool
    {
        $regex = '/^(?=.*\d)(?=.*[a-zA-Z])';
        $regex .= '.{' . $min_len . ',' . $max_len . '}$/';
        if (preg_match($regex, $password)) {
            return true;
        } else {
            return false;
        }
    }

    private function HandlePostType(string $name, string $type, string $more_info): string
    {
        if (empty($regex = $this->regex_patterns::Patterns($type))) {
            $regex = $this->Patterns($type);
        }
        //        if(in_array($type, self::keys())){
        switch ($type) {
            case 'email';
                return self::EmailValidation($_POST[$name], $name);
            case 'ip';
                return self::IPValidation($_POST[$name], $name);
            case 'phone';
                if (! preg_match($this->regex_patterns::Patterns($type), $_POST[$name]) && ! preg_match($this->regex_patterns::Patterns('phone_full'), $_POST[$name])) {
                    Json::Invalid($name, $more_info, self::$line);

                    return '';
                }
                $ph = PhoneNumberValidation::getInstance()->SetRegion()->SetNumber($_POST[$name]);
                if ($ph->NumberIsValid()) {
                    try {
                        $phone = $ph->NumberFormatE164();
                        if (str_contains($phone, '+20')) {
                            if (! in_array(substr($phone, 3, 2), [10, 11, 12, 15])) {
                                Json::Invalid('phone', $more_info, self::$line);
                            }
                        }

                        return $phone;
                    } catch (NumberParseException $e) {
                        Logger::RecordLog($e, 'post_validator_phone');
                        Json::TryAgain();
                        exit();
                    }
                } else {
                    Json::Invalid($name, $more_info, self::$line);
                    exit();
                }

            case 'mobile_egypt';
                if (! preg_match($this->regex_patterns::Patterns($type), $_POST[$name])
                    || ! in_array(substr($_POST[$name], 1, 2), [10, 11, 12, 15])
                    || strlen((int)$_POST[$name]) < 10) {
                    Json::Invalid($name, $more_info, self::$line);

                    return '';
                }

                return $_POST[$name];

            case 'status';
                if (strtolower($_POST[$name]) !== 'all' && ! empty($regex) && ! preg_match($regex, $_POST[$name])) {
                    Json::Invalid($name, $more_info, self::$line);
                    exit();
                }
                return $_POST[$name];

            case 'day';
            case 'month';
                if (! is_numeric($_POST[$name]) && $_POST[$name] <= 0) {
                    Json::Invalid($name, $more_info, self::$line);
                    exit();
                } else {
                    if ($_POST[$name] > 9) {
                        if (! empty($regex)) {
                            if (! preg_match($regex, $_POST[$name])) {
                                Json::Invalid($name, $more_info, self::$line);
                                exit();
                            }
                        }
                    } else {
                        return '0' . $_POST[$name];
                    }
                }
                break;

            case 'float';
            case 'int';
                if (! is_numeric($_POST[$name])) {
                    Json::Invalid($name, $more_info, self::$line);
                    exit();
                }
                break;

            default;
//                if (empty($regex = $this->regex_patterns::Patterns($type))) {
//                    $regex = $this->Patterns($type);
//                }
                if (! empty($regex)) {
                    if (! preg_match($regex, $_POST[$name])) {
                        Json::Invalid($name, $more_info, self::$line);
                        exit();
                    }
                }
        }

        //        }

        return self::ClearInput($_POST[$name]);
    }

    /*
        public function PasswordStrengthCheck($password, $min_len = 8, $max_len = 70, $req_digit = 1, $req_lower = 1, $req_upper = 1, $req_symbol = 1): bool
        {
            // Build regex string depending on requirements for the password
            $regex = '/^';
            if ($req_digit == 1) { $regex .= '(?=.*\d)'; }              // Match at least 1 digit
            if ($req_lower == 1) { $regex .= '(?=.*[a-z])'; }           // Match at least 1 lowercase letter
            if ($req_upper == 1) { $regex .= '(?=.*[A-Z])'; }           // Match at least 1 uppercase letter
    //        if ($req_symbol == 1) { $regex .= '(?=.*[^a-zA-Z\d])'; }    // Match at least 1 character that is none of the above
            if ($req_symbol == 1) { $regex .= '(?=.*[!@#$%\s])'; }    // Match at least 1 character that is none of the above
            $regex .= '.{' . $min_len . ',' . $max_len . '}$/';

    //        if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[\dA-Za-z!@#$%\s]{8,70}$/', $password)) {
    //            echo 'the password does not meet the requirements!';
    //        }

            if(preg_match($regex, $password)) {
                return true;
            } else {
                return false;
            }
        }*/


}