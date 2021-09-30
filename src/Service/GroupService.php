<?php

namespace App\Service;

use RetailCrm\Api\Factory\SimpleClientFactory;
use RetailCrm\Api\Model\Request\Users\UserGroupsRequest;

class GroupService
{
    private $client;

    public function __construct()
    {
        $this->client = SimpleClientFactory::createClient('https://patronatoalpormayor.simla.com', 'DXRkUwBQd8hCIsH9aQM2U7tTc8CGOEMh');
    }

    public function findGroupById(int $id)
    {
        $groupRequest = new UserGroupsRequest();
        $groups = $this->client->users->userGroups($groupRequest)->groups;
        foreach ($groups as $group) {
            if ($group->id == $id) {
                return $group;
            }
        }
        return null;
    }
}