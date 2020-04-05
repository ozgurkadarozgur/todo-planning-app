<?php
/**
 * Created by PhpStorm.
 * User: ozgur
 * Date: 05.04.2020
 * Time: 01:32
 */

namespace App\Service;


use App\Repository\TodoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class TodoPlanningService
{

    private $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    public function group_by_level(Array $todo_list)
    {
        $result = array();

        foreach ($todo_list as $value) {
            $result[$value->getLevel()][] = $value;
        }
        return $result;
    }

    public function calculate_total_time_by_level(Array $grouped_todo_list)
    {
        $result = array();

        foreach ($grouped_todo_list as $key => $value){
            if (empty($result[$key])) $result[$key] = 0;
            foreach ($value as $item) {
                $result[$key] += $item->getEstimatedDuration();
            }
        }

        return $result;
    }

    public function find_min_estimated_time(Array $total_time_by_level)
    {
        $min = min($total_time_by_level);
        return [
            'key' => array_keys($total_time_by_level, $min)[0],
            'value' => $min,
        ];
    }

    public function find_max_estimated_time(Array $total_time_by_level)
    {
        $max = max($total_time_by_level);
        return [
            'key' => array_keys($total_time_by_level, $max)[0],
            'value' => $max,
        ];
    }

    public function get_todo_list_by_level(ArrayCollection $arr, $level)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('level', $level))
            ->orderBy(['estimated_duration' => Criteria::DESC]);

        $result = $arr->matching($criteria)->toArray();
        if (count($result) == 0 and $level > 1) {
            return self::get_todo_list_by_level($arr, $level -1);
        }
        return $result;
    }

    public function check_in_matched_jobs($id)
    {
        $result = array_search($id, DeveloperService::$matched_jobs);
        return $result;
    }

    public function calculate_estimated_duration($level, $new_level, $time)
    {
        if ($level == $new_level) return $time;
        return (int)$new_level * $time / $level;
    }

    public function calculate_total_week_to_finish(Array $weekly_plan)
    {

        $time_by_developer = [];
        $total = 0;
        foreach (DeveloperService::$arr as $developer) {
            foreach ($weekly_plan[$developer["name"]] as $week) {
                foreach ($week as $todo) {
                    $total += $todo->work_time;
                }
            }
            $time_by_developer[$developer["name"]] = $total;
            $total = 0;
        }
        $max = self::find_max_estimated_time($time_by_developer);
        $max_time = $max["value"];
        return $max_time;
    }

    public function weekly_plan(Array $arr)
    {
        $plan = [];
        $week = 1;
        $total_time = 0;
        foreach ($arr as $item) {
            $total_time += $item->takes_time;
            if (empty($plan[$week])) $plan[$week] = [];
            if ($total_time > 45) {
                $remaining_time = $total_time - 45;
                $item->work_time = $item->takes_time - $remaining_time;
                $item->remaining_time = $remaining_time;
                array_push($plan[$week], $item);

                $week++;

                if (empty($plan[$week])) $plan[$week] = [];
                $new_item = (object)[
                    'id' => $item->id,
                    'title' => $item->title,
                    'takes_time' => $item->takes_time,
                    'level' => $item->level,
                    'estimated_duration' => $item->estimated_duration,
                    'work_time' => $item->remaining_time,
                    'remaining_time' => 0,
                ];
                array_push($plan[$week], $new_item);
                $total_time = $remaining_time;
            } else {
                if (empty($plan[$week])) $plan[$week] = [];
                $item->work_time = $item->takes_time;
                $item->remaining_time = 0;
                array_push($plan[$week], $item);
            }
        }
        return $plan;
    }

    public function process(Array $todo_list, $min)
    {
        $remaining_jobs = [];
        foreach (array_reverse(DeveloperService::$arr) as $dev) {
            $level_jobs = $this->get_todo_list_by_level(new ArrayCollection($todo_list), $dev["level"]);
            //echo 'level: ' . $dev["level"] .' job_count' . count($level_jobs) . '<br>';
            if (count($level_jobs) > 0){
                $total_time = 0;
                foreach ($level_jobs as $item) {
                    if (!self::check_in_matched_jobs($item->getId())) {
                        $total_time += $item->getEstimatedDuration();
                        if ($total_time <= $min['value']) {
                            array_push(DeveloperService::$matched_jobs, $item->getId());
                            array_push(DeveloperService::$todo_by_developer_arr[$dev["name"]], (object)[
                                'id' => $item->getId(),
                                'title' => $item->getTitle(),
                                'takes_time'=> self::calculate_estimated_duration($dev["level"], $item->getLevel(), $item->getEstimatedDuration()),
                                'level' => $item->getLevel(),
                                'estimated_duration' => $item->getEstimatedDuration(),
                            ]);
                        } else {
                            array_push($remaining_jobs, $item);
                        }
                    }
                }
            } else {
                echo 'bos geldi' . '<br>';
            }
        }
        if (count($remaining_jobs) > 0){
            $grouped = self::group_by_level($remaining_jobs);
            $total_time_by_level = self::calculate_total_time_by_level($grouped);
            $min = self::find_min_estimated_time($total_time_by_level);
            return self::process($remaining_jobs, $min);
        }
        return $remaining_jobs;
    }

    public function run()
    {
        $todo_list = $this->todoRepository->all();
        $grouped_todo_list = self::group_by_level($todo_list);
        $total_time_by_level = self::calculate_total_time_by_level($grouped_todo_list);
        $min_estimated_time = self::find_min_estimated_time($total_time_by_level);
        self::process($todo_list, $min_estimated_time);
    }

}