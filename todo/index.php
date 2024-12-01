<?php
session_start();

require_once __DIR__ . '/controllers/TaskController.php';
require_once __DIR__ . '/controllers/UserController.php';

$taskController = new TaskController();
$userController = new UserController();

// Basic routing logic
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'register':
            $userController->register();
            break;
        case 'login':
            $userController->login();
            break;
        case 'logout':
            $userController->logout();
            break;
        case 'addTask':
            $taskController->addTask();
            break;
        case 'editTask':
            $taskController->editTask();
            break;
        case 'deleteTask':
            $taskController->deleteTask();
            break;
        case 'completeTask':
            $taskController->completeTask();
            break;
        default:
            $taskController->showTasks();
            break;
    }
} else {
    $taskController->showTasks();
}

?>
