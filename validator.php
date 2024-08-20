<?php
class Validator {
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isValidAmount($amount) {
        return is_numeric($amount) && $amount > 0;
    }
}
