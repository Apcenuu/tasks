<?php

namespace App\Service;

use RetailCrm\Api\Enum\ByIdentifier;
use RetailCrm\Api\Factory\SimpleClientFactory;
use RetailCrm\Api\Model\Entity\Tasks\Task;
use RetailCrm\Api\Model\Request\BySiteRequest;
use RetailCrm\Api\Model\Request\Tasks\TasksRequest;

class TasksService
{
    private $client;
    /**
     * @var GroupService
     */
    private $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->client = SimpleClientFactory::createClient('https://patronatoalpormayor.simla.com', 'DXRkUwBQd8hCIsH9aQM2U7tTc8CGOEMh');
        $this->groupService = $groupService;
    }


    public function getTasksArray()
    {
        $tasks = $this->getTasks();

        $now = new \DateTime();
//        $now->modify('-1 day');
        $rows = [];
        foreach ($tasks as $task) {
            if (!isset($task->datetime)) {
                continue;
            }
            if ($task->datetime->format('d') < $now->format('d')) {
                return $rows;
            }

            $row = [
                'date' => null,
                'task' => $task->text,
                'customer' => $task->customer->site,
                'phone' => null,
                'performer' => null
            ];

            if ($task->performer) {
                $row['performer'] = $this->getPerformer($task);
            }

            if ($task->datetime) {
                $row['date'] = $task->datetime->format('d.m.Y H:i:s');
            }

            $row['phone'] = $this->getClientPhone($task);

            $rows[] = $row;



        }
        return $rows;
    }

    private function getClientPhone(Task $task)
    {
        $customerRequest = (new BySiteRequest(ByIdentifier::ID));
        $customerResponse = $this->client->customers->get($task->customer->id, $customerRequest);
        if (count($customerResponse->customer->phones) > 0) {
             return array_shift($customerResponse->customer->phones)->number;
        }
        return null;
    }

    private function getTasks(): array
    {
        $request = new TasksRequest();
        $request->limit = 100;
        return $this->client->tasks->list($request)->tasks;
    }

    private function getPerformer(Task $task): string
    {
        if ($task->performerType == 'group') {
            $group = $this->groupService->findGroupById($task->performer);
            $performer = $group->name;
        }
        if ($task->performerType == 'user') {
            $performer = $this->client->users->get($task->performer)->user->firstName;
        }
        return $performer;
    }
}