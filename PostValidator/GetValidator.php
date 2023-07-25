<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-10-22
 * Time: 1:24 PM
 * https://www.Maatify.dev
 */

namespace Maatify\PostValidator;

use \App\Assist\RegexPatterns;
use Maatify\Json\Json;

class GetValidator
{
    private static self $instance;

    public static function obj(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected static int|string $line;
    private RegexPatterns $regex_patterns;


    public function ValidateGet(string $name, string $type): bool|int
    {
        return preg_match($this->regex_patterns::Patterns($type), $_GET[$name]);
    }

    public function Optional(string $name, string $type, string $more_info = ''): string
    {
        if(!empty($_GET) && !empty($_GET[$name]) && !is_array($_GET[$name])){
            return $this->HandleGetType($name, $type, $more_info);
        }else{
            if(in_array($type, ['page', 'limit'])){
                if (empty($_GET[$name]) || !is_numeric($_GET[$name])) {
                    return match ($type) {
                        'page' => 1,
                        'limit' => 25,
                    };
                }
            }
        }
        return '';
    }

    private function HandleGetType(string $name, string $type, string $more_info): string
    {
        if(!empty($regex = $this->regex_patterns::Patterns($type))){
            if(!preg_match($regex, $_GET[$name])){
                Json::Invalid($name, $more_info, self::$line);
                exit();
            }
        }
        return self::ClearInput($_GET[$name]);
    }

    private function ClearInput($data): string
    {
        $data = trim($data);
        $data = stripslashes($data);
        return htmlspecialchars($data,ENT_QUOTES|ENT_SUBSTITUTE);
    }
}