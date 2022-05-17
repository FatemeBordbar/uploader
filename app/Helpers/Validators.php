<?php namespace App\Helpers;

class Validators
{
    public static function generate($pattern)
    {
        $returnee = "";
        $pattern = explode('|', $pattern);
        foreach ($pattern as $rule) {
            $attribute = strtolower($rule);
            if (strpos($attribute, ':') >= 1) {
                $attribute = explode(':', $attribute);
                $clause = $attribute[1];
                $attribute = $attribute[0];
            }
            switch ($attribute) {
                case 'required':
                    $returnee .= " required data-parsley-required='true' ";
                    break;
                case 'email':
                    $returnee .= " type='email' data-parsley-type='email' ";
                    break;
                case 'min':
                    $returnee .= " minlength='$clause' data-parsley-minlength='$clause' ";
                    break;
                case 'max':
                    $returnee .= " maxlength='$clause' data-parsley-maxlength='$clause' ";
                    break;
                case 'url':
                    $returnee .= " type='url' data-parsley-type='url' ";
                    break;
                case 'integer':
                    $returnee .= " type='number' data-parsley-type='integer' data-parsley-type='number' ";
                    break;
                case 'num':
                case 'numeric':
                case 'digits':
                    $returnee .= " data-parsley-type='number' ";
                case 'string':
                    $returnee .= " data-parsley-type='alphanum' ";
                    break;
                case 'minval':
                    $returnee .= " min='$clause' data-parsley-min='$clause' ";
                    break;
                case 'maxval':
                    $returnee .= " max='$clause' data-parsley-max='$clause' ";
                    break;
                case 'date':
                    $returnee .= " data-parsley-type='date' ";
                    break;
                case 'length':
                    $returnee .= " pattern='.{" . $clause . "}'";
            }
        }
        $returnee .= " data-parsley-trigger='change'";
        return $returnee;

    }

    public static function process($validators)
    {
        $returnee = [];
        if (is_null($validators) || count($validators) == 0) {
            return [];
        }

        foreach ($validators as $key => $value) {
            $returnee[$key] = self::generate($value);
        }
        return $returnee;
    }
}
