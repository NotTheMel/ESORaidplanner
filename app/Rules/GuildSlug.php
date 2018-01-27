<?php

/**
 * This file is part of the ESO Raidplanner project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/Woeler/eso-raid-planner
 */

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class GuildSlug implements Rule
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
        $passed  = true;

        if ($value !== strtolower($value)) {
            $passed = false;
        }

        if (false !== strpos($value, ' ')) {
            $passed = false;
        }

        return $passed;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please only use lowercase letters for your slug, also spaces are not allowed.';
    }
}
