<?php

namespace App\Controller;

use App\Provider\Provider1;
use App\Repository\TodoRepository;
use App\Service\DeveloperService;
use App\Service\ProviderService;
use App\Service\TodoPlanningService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{

    private $todoPlanningService;

    public function __construct(TodoPlanningService $todoPlanningService)
    {
        $this->todoPlanningService = $todoPlanningService;
    }

    /**
     * @Route("/todo", name="todo")
     */
    public function index()
    {
        $this->todoPlanningService->run();
        $todo_by_developer_arr = DeveloperService::$todo_by_developer_arr;
        $developers = array_keys($todo_by_developer_arr);

        $weekly_plan = [];

        foreach ($developers as $developer) {
            $weekly_plan[$developer] = $this->todoPlanningService->weekly_plan($todo_by_developer_arr[$developer]);
        }

        $max_hour = $this->todoPlanningService->calculate_total_week_to_finish($weekly_plan);

        $max_week = round($max_hour / 45, 1);

        return $this->render('todo/index.html.twig', compact('developers','weekly_plan', 'max_hour', 'max_week'));
    }
}
