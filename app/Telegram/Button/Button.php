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

namespace App\Telegram\Button;

abstract class Button
{
    public $buttonText;
    public $buttonData;

    /**
     * Button constructor.
     *
     * @param $buttonText
     * @param array $buttonData
     */
    public function __construct($buttonText, $buttonData = [])
    {
        $this->buttonText = $buttonText;
        $this->buttonData = $buttonData;
    }

    /**
     * @param $sender
     *
     * @return mixed
     */
    abstract public function buttonClicked($sender);
}
