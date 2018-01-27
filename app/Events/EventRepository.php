<?php

/**
 * Created by PhpStorm.
 * User: Woeler
 * Date: 31-7-2017
 * Time: 03:46.
 */

namespace Woeler\EsoRaidPlanner\Repository;

use DateTime;
use Woeler\EsoRaidPlanner\Configuration\Database;
use Woeler\EsoRaidPlanner\Domain\Model\Event;
use Woeler\EsoRaidPlanner\Singleton\MySQL;

class EventRepository
{
    private $database;

    /**
     * EventRepository constructor.
     */
    public function __construct()
    {
        $this->database = Database::getInstance()->getConnection();
    }

    public function get(int $id): Event
    {
        $result = $this->database->debug()->select(MySQL::EVENTS, '*', [
             'id' => $id,
         ]);
        var_dump($this->database->error());
        var_dump($result);

        $event = new Event($result['name'], $result['description'], new DateTime($result['start_date']), $result['type']);
        $event->setId($id);

        return $event;
    }

    public function create(Event $event)
    {
        $this->database->insert(MySQL::EVENTS, [
            'name'        => $event->getName(),
            'description' => $event->getDescription(),
            'start_date'  => $event->getStartDateFormatted(),
            'type'        => $event->getType(),
        ]);
    }

    public function delete(int $id)
    {
        $this->database->update(MySQL::EVENTS, [
            'deleted' => 1,
            ], [
            'id' => $id,
        ]);
    }

    public function modify(int $id, Event $event)
    {
        $this->database->update(MySQL::EVENTS, [
            'name'        => $event->getName(),
            'description' => $event->getDescription(),
            'start_date'  => $event->getStartDateFormatted(),
            'type'        => $event->getType(),
        ], [
            'id' => $id,
        ]);
    }

    public function getAfterDate(DateTime $date, int $limit): array
    {
        $result = $this->database->select(MySQL::EVENTS, [
            '*',
        ], [
            'start_date[>]' => $date->format('Y-m-d H:i:s'),
            'ORDER'         => ['start_date' => 'ASC'],
        ]);

        $eventArray = [];

        $i = 0;
        foreach ($result as $eventData) {
            if ($i >= $limit) {
                break;
            }
            $newEvent = new Event($eventData['name'], $eventData['description'], new DateTime($eventData['start_date']), (int) $eventData['type']);
            $newEvent->setId((int) $eventData['id']);
            $eventArray[$i] = $newEvent;
            ++$i;
        }

        return $eventArray;
    }

    public function getBetweenDates(DateTime $start, DateTime $end): array
    {
        $result = $this->database->select(MySQL::EVENTS, [
            '*',
        ], [
            'start_date[>]' => $start->format('Y-m-d H:i:s'),
            'start_date[<]' => $end->format('Y-m-d H:i:s'),
            'ORDER'         => ['start_date' => 'ASC'],
        ]);

        $eventArray = [];

        $i = 0;
        foreach ($result as $eventData) {
            $newEvent = new Event($eventData['name'], $eventData['description'], new DateTime($eventData['start_date']), (int) $eventData['type']);
            $newEvent->setId((int) $eventData['id']);
            $eventArray[$i] = $newEvent;
            ++$i;
        }

        return $eventArray;
    }
}
