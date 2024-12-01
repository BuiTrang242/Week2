<?php

require_once __DIR__ . '/../models/TaskModel.php';
require_once __DIR__ . '/../controllers/UserController.php';

class TaskController {
    private $taskModel;
    private $userController;

    public function __construct() {
        $this->taskModel = new TaskModel();
        $this->userController = new UserController();
    }

    private function checkAuth() {
        if (!$this->userController->isLoggedIn()) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    public function showTasks() {
        $this->checkAuth();
        
        $userId = $_SESSION['user_id'];
        $search = $_GET['search'] ?? '';
        $priority = $_GET['priority'] ?? null;
        
        $tasks = $this->taskModel->searchTasks($userId, $search, $priority);
        
        // Separate tasks into completed and incomplete
        $completedTasks = array_filter($tasks, function($task) {
            return $task['completed'];
        });
        
        $incompleteTasks = array_filter($tasks, function($task) {
            return !$task['completed'];
        });
        
        require __DIR__ . '/../views/tasks.php';
    }

    public function addTask() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $priority = $_POST['priority'] ?? 'medium';
            
            if (empty($title)) {
                $_SESSION['error'] = 'Title is required';
                header('Location: index.php');
                exit;
            }
            
            $this->taskModel->addTask(
                $_SESSION['user_id'],
                $title,
                $content,
                $priority
            );
            
            $_SESSION['success'] = 'Task added successfully';
            header('Location: index.php');
            exit;
        }
    }

    public function editTask() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskId = $_POST['task_id'] ?? '';
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $priority = $_POST['priority'] ?? 'medium';
            
            if (empty($taskId) || empty($title)) {
                $_SESSION['error'] = 'Invalid task data';
                header('Location: index.php');
                exit;
            }
            
            $this->taskModel->updateTask($taskId, [
                'title' => $title,
                'content' => $content,
                'priority' => $priority
            ]);
            
            $_SESSION['success'] = 'Task updated successfully';
            header('Location: index.php');
            exit;
        }
    }

    public function deleteTask() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskId = $_POST['task_id'] ?? '';
            
            if (empty($taskId)) {
                $_SESSION['error'] = 'Invalid task ID';
                header('Location: index.php');
                exit;
            }
            
            $this->taskModel->deleteTask($taskId);
            
            $_SESSION['success'] = 'Task deleted successfully';
            header('Location: index.php');
            exit;
        }
    }

    public function completeTask() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskId = $_POST['task_id'] ?? '';
            
            if (empty($taskId)) {
                $_SESSION['error'] = 'Invalid task ID';
                header('Location: index.php');
                exit;
            }
            
            $this->taskModel->toggleComplete($taskId);
            
            $_SESSION['success'] = 'Task status updated successfully';
            header('Location: index.php');
            exit;
        }
    }
}
