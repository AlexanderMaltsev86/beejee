<?php
namespace App\Controllers;


use App\Framework\DI;
use App\Framework\View;
use App\Models\Task;
use InvalidArgumentException;

class TasksController extends BaseController {
    public function indexAction() {
        $page = $this->getRequestParam('page');
        $order = $this->getRequestParam('order');
        $direction = $this->getRequestParam('direction');
        $action = $this->getRequestParam('action');
        if (!$page || $page < 1) {
            $page = 1;
        }
        $totalCount = Task::count();
        switch ($order) {
            case 'name':
            case 'email':
            case 'completed':
                $sortedColumn = $order;
                break;
            default:
                $sortedColumn = 'id';
                break;
        }

        $tasks = Task::find([
            'LIMIT' => [($page-1) * 3, 3],
            'ORDER' => [
                $sortedColumn => $direction === 'desc' ? 'DESC' : 'ASC'
            ],
        ]);
        return new View('tasks.list', [
            'tasks' => $tasks,
            'page' => $page,
            'pagesCount' => ceil($totalCount / 3.0),
            'order' => $order,
            'direction' => $direction,
            'action' => $action
        ]);
    }

    public function formAction() {
        $taskId = $this->getRequestParam('id');
        $page = $this->getRequestParam('page');
        $order = $this->getRequestParam('order');
        $direction = $this->getRequestParam('direction');
        if ($taskId) {
            $task = Task::findFirst([
                'id' => $taskId
            ]);
        } else {
            $task = new Task();
        }
        return new View('tasks.form', [
            'task' => $task,
            'page' => $page,
            'order' => $order,
            'direction' => $direction
        ]);
    }

    public function submitAction() {
        $taskId = $this->getRequestParam('id');
        $page = $this->getRequestParam('page');
        $order = $this->getRequestParam('order');
        $direction = $this->getRequestParam('direction');

        if ($taskId) {
            $task = Task::findFirst([
                'id' => $taskId
            ]);
            if ($task === null) {
                throw new InvalidArgumentException("Task with id $taskId not found");
            }
            if ($task->text !== $this->getRequestParam('text')) {
                $currentUser = DI::getInstance()->get('requestContext')->getVariable('user');
                $task->updated_by = $currentUser->login;
            }
        } else {
            $task = new Task();
        }
        $task->name = $this->getRequestParam('name');
        $task->email = $this->getRequestParam('email');
        $task->text = $this->getRequestParam('text');
        $task->completed = $this->getRequestParam('completed');
        if (!$task->completed) {
            $task->completed = false;
        }

        if ($taskId) {
            $task->id = $taskId;
            $task->update();
        } else {
            $task->create();
        }
        if ($taskId) {
            $this->redirect("/?page=$page" . ($order ? "&order=$order&direction=$direction" : ''));
        } else {
            $this->redirect("/?action=added&page=$page" . ($order ? "&order=$order&direction=$direction" : ''));
        }
    }

    public function deleteAction() {
        $taskId = $this->getRequestParam('id');
        $page = $this->getRequestParam('page');
        $order = $this->getRequestParam('order');
        $direction = $this->getRequestParam('direction');
        if (is_numeric($taskId)) {
            $task = Task::findFirst([
                'id' => $taskId
            ]);
            if ($task === null) {
                throw new InvalidArgumentException("Task with id $taskId not found");
            }
            $task->delete();
        }
        $this->redirect("/?page=$page" . ($order ? "&order=$order&direction=$direction" : ''));
    }
}