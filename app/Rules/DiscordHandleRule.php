<?php

namespace App\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class DiscordHandleRule implements Rule
{
    private $message;

    /**
     * Create a new rule instance.
     */
    public function __construct()
    {
        $this->message = 'You did not set a valid Discord handle. A valid handle looks something like this: Yourname#1234.';
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
        $u = User::query()->where('discord_handle', '=', $value)->first();
        if (null !== $u && Auth::id() !== $u->id) {
            $this->message = 'Someone else is already using the Discord handle '.$value.'.';

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
        return $this->message;
    }
}
