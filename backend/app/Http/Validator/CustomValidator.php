<?php
namespace App\Http\Validator;

class CustomValidator extends \Illuminate\Validation\Validator
{

    public function validatePhone($attribute, $value, $parameters)
    {
        // Phone number should start with number 0-9 and can have minus, plus
        // and braces.
        return preg_match("/^([0-9\s\-\+\(\)]*)$/", $value);
    }

    public function validateMobile($attribute, $value, $parameters)
    {
        // Mobile number can start with plus sign and should start with number
        // and can have minus sign and should be between 9 to 12 character long.
        return preg_match("/^\+?\d[0-9-]{9,12}/", $value);
    }

    public function validateCsv($attribute, $value, $parameters)
    {
        // Valide comman separated value.
        return preg_match("/[A-Za-z0-9\s]+(,[A-Za-z0-9\s]+)*[A-Za-z0-9]$/", $value);
    }

    public function validateMonthYear($attribute, $value, $parameters)
    {
        // Can have 3 letter month name as string followed by 4 letter year
        // number.
        return preg_match("/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sept|Oct|Nov|Dec)-[0-9]{4}$/i", $value);
    }

    public function validateFloat($attribute, $value, $parameters)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND) ? true : false;
    }

    public function validateDecimal($attribute, $value, $parameters)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND) ? true : false;
    }

    public function validateAlphaNumCustom($attribute, $value, $parameters)
    {
        // string can be 0-9 A-z and may have space and dash
        return preg_match("/^[A-Za-z0-9\-\s]+$/", $value);
    }

    public function validateAlphaNumSpecial($attribute, $value, $parameters)
    {
        // string can be 0-9 A-z and may have space and dash and some special characters
        return preg_match("/^[A-Za-z0-9\-!@$%&*()_~\s]+$/", $value);
    }

    public function validateAlphaCustom($attribute, $value, $parameters)
    {
        // string can be 0-9 A-z and may have space and dash
        return preg_match("/^[A-Za-z\-\s]+$/", $value);
    }

    public function validatePrice($attribute, $value, $parameters)
    {
        return filter_var($value,FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND) === false ? false : true;
    }

    public function validateDecimalPoint($attribute, $value, $parameters)
    {
        // string can be 0-9 A-z and may have space and dash
        return preg_match("/^[0-9]+(\.[0-9]{1,4})?$/", $value);
    }

    public function validateTitleLimit($attribute, $value, $parameters)
    {
        if(strlen($value) <= config('constants.TITLE_LIMIT'))
            return true;
    }

    public function validateDescriptionLimit($attribute, $value, $parameters)
    {
        if(strlen($value) <= config('constants.DESC_LIMIT'))
            return true;
    }

    /**
     * Compare two dates where $value is greater date
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateDateGreater($attribute, $value, $parameters)
    {
        if (isset($parameters[1])) {
            $other = $parameters[1];
             return strtotime($value) >= strtotime($other);
        } else {
                return true;
            }
    }

    public function validateValueGreater($attribute, $value, $parameters)
    {
        if (isset($parameters[1])) {
            $other = $parameters[1];
            return ($value) >= ($other);
        } else {
            return true;
        }
    }



}