<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DiscordHandleRule implements Rule
{
    /**
     * Create a new rule instance.
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return true;
        }

        if (false === strpos($value, '#')) {
            return false;
        }

        $expl = explode('#', $value, 2);

        if (!is_numeric($expl[1]) || 4 !== strlen($expl[1])) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You did not set a valid Discord handle. A valid handle looks something like this: Yourname#1234.';
    }
}
