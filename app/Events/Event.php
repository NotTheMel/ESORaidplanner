<?php

/**
 * Created by PhpStorm.
 * User: Woeler
 * Date: 31-7-2017
 * Time: 03:38.
 */

namespace App\Events;

use DateTime;

class Event
{
    private $id;

    private $name;

    private $description;

    private $startDate;

    private $type;

    private $signups;

    /**
     * Event constructor.
     *
     * @param string   $name
     * @param string   $description
     * @param DateTime $startDate
     * @param int      $type
     */
    public function __construct(string $name, string $description, DateTime $startDate, int $type)
    {
        $this->name        = $name;
        $this->description = $description;
        $this->startDate   = $startDate;
        $this->type        = $type;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @param DateTime $startDate
     */
    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getSignups(): array
    {
        return $this->signups;
    }

    /**
     * @param array $signups
     */
    public function setSignups(array $signups)
    {
        $this->signups = $signups;
    }

    /**
     * @return int
     */
    public function getTotalSignups(): int
    {
        return count($this->getSignups());
    }

    public function getStartDateFormatted(): string
    {
        return $this->getStartDate()->format('Y-m-d H:i:s');
    }
}
