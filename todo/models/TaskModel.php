<?php

class TaskModel {
    private $filePath;

    public function __construct() {
        $this->filePath = __DIR__ . '/../data/tasks.json';
    }

    public function getTasks() {
        if (file_exists($this->filePath)) {
            $jsonData = file_get_contents($this->filePath);
            return json_decode($jsonData, true) ?? [];
        }
        return [];
    }

    public function saveTasks($tasks) {
        file_put_contents($this->filePath, json_encode($tasks, JSON_PRETTY_PRINT));
    }

    public function getUserTasks($userId) {
        $tasks = $this->getTasks();
        return array_filter($tasks, function($task) use ($userId) {
            return $task['user_id'] === $userId;
        });
    }

    public function addTask($userId, $title, $content, $priority) {
        $tasks = $this->getTasks();
        $tasks[] = [
            'id' => uniqid(),
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'priority' => $priority,
            'completed' => false,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->saveTasks($tasks);
        return true;
    }

    public function updateTask($taskId, $data) {
        $tasks = $this->getTasks();
        foreach ($tasks as &$task) {
            if ($task['id'] === $taskId) {
                $task = array_merge($task, $data);
                break;
            }
        }
        $this->saveTasks($tasks);
        return true;
    }

    public function deleteTask($taskId) {
        $tasks = $this->getTasks();
        $tasks = array_filter($tasks, function($task) use ($taskId) {
            return $task['id'] !== $taskId;
        });
        $this->saveTasks(array_values($tasks));
        return true;
    }

    public function toggleComplete($taskId) {
        $tasks = $this->getTasks();
        foreach ($tasks as &$task) {
            if ($task['id'] === $taskId) {
                $task['completed'] = !$task['completed'];
                break;
            }
        }
        $this->saveTasks($tasks);
        return true;
    }

    public function searchTasks($userId, $query = '', $priority = null) {
        $tasks = $this->getUserTasks($userId);
        
        return array_filter($tasks, function($task) use ($query, $priority) {
            $matchesQuery = empty($query) || 
                stripos($task['title'], $query) !== false || 
                stripos($task['content'], $query) !== false;
                
            $matchesPriority = $priority === null || $task['priority'] === $priority;
            
            return $matchesQuery && $matchesPriority;
        });
    }
}
