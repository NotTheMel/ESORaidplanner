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
 * @see https://github.com/ESORaidplanner/ESORaidplanner
 */

namespace App\Telegram\Button;

class GoBackButton extends Button
{
    /** @var Menu *
     * @return mixed
     */
    public function buttonClicked($sender)
    {
        return $sender->getParentMenu();
    }
}
