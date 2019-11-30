<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Pdp\Domain;

class DomainValidator implements Rule
{
    public function passes($attribute, $value)
    {
        try {
            new Domain($value);

            return true;
        } catch (\Exception $e) {
        }

        return false;
    }

    public function message()
    {
        return 'The :attribute is not a valid domain.';
    }
}
