<?php

require_once 'framework/Model.php';

class Validation {

    public static function validate_email(string $value) : bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validate_date(string $value) : bool {
        return strtotime($value) !== false;
    }

}