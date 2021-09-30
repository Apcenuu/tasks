<?php

namespace App\Command;

use App\Service\ExcelService;
use App\Service\TasksService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UploadTasksCommand extends Command
{
    protected static $defaultName = 'tasks:upload';
    /**
     * @var ExcelService
     */
    private $excelService;
    /**
     * @var TasksService
     */
    private $tasksService;

    public function __construct(string $name = null, ExcelService $excelService, TasksService $tasksService)
    {
        parent::__construct($name);

        $this->excelService = $excelService;
        $this->tasksService = $tasksService;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rows = $this->tasksService->getTasksArray();
        $this->excelService->writeXLSX('tasks.xlsx', $rows);

        return 0;
    }

}