<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .priority-high { border-left: 4px solid #dc3545; }
        .priority-medium { border-left: 4px solid #ffc107; }
        .priority-low { border-left: 4px solid #28a745; }
        .task-item { 
            padding: 10px 15px;
            margin-bottom: 8px;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }
        .task-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 500;
        }
        .task-content {
            margin: 0;
            font-size: 0.9rem;
            color: #666;
        }
        .task-actions {
            display: flex;
            gap: 5px;
        }
        .btn-xs {
            padding: 0.1rem 0.4rem;
            font-size: 0.875rem;
        }
        .completed-task {
            opacity: 0.7;
        }
        .completed-task .task-title,
        .completed-task .task-content {
            text-decoration: line-through;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Todo List</a>
            <div class="ml-auto">
                <span class="navbar-text mr-3">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="index.php?action=logout" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Task Controls -->
        <div class="row mb-4">
            <div class="col-md-6">
                <!-- Search and Filter -->
                <form method="get" class="form-inline">
                    <input type="text" class="form-control form-control-sm mr-2" name="search" placeholder="Search tasks" 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <select class="form-control form-control-sm mr-2" name="priority">
                        <option value="">All Priorities</option>
                        <option value="high" <?php echo isset($_GET['priority']) && $_GET['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                        <option value="medium" <?php echo isset($_GET['priority']) && $_GET['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="low" <?php echo isset($_GET['priority']) && $_GET['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-secondary">Search</button>
                </form>
            </div>
            <div class="col-md-6 text-right">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addTaskModal">
                    <i class="fas fa-plus"></i> Add New Task
                </button>
            </div>
        </div>

        <div class="row">
            <!-- Incomplete Tasks -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Incomplete Tasks</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($incompleteTasks as $task): ?>
                                <div class="list-group-item task-item priority-<?php echo htmlspecialchars($task['priority']); ?>">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h6>
                                            <?php if (!empty($task['content'])): ?>
                                                <p class="task-content"><?php echo htmlspecialchars($task['content']); ?></p>
                                            <?php endif; ?>
                                            <small class="text-muted">Priority: <?php echo ucfirst(htmlspecialchars($task['priority'])); ?></small>
                                        </div>
                                        <div class="task-actions">
                                            <form action="index.php?action=completeTask" method="post" style="display: inline;">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <button type="submit" class="btn btn-success btn-xs" title="Complete">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" 
                                                    data-target="#editTask<?php echo $task['id']; ?>" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="index.php?action=deleteTask" method="post" style="display: inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-xs" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Tasks -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Completed Tasks</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($completedTasks as $task): ?>
                                <div class="list-group-item task-item completed-task">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h6>
                                            <?php if (!empty($task['content'])): ?>
                                                <p class="task-content"><?php echo htmlspecialchars($task['content']); ?></p>
                                            <?php endif; ?>
                                            <small class="text-muted">Priority: <?php echo ucfirst(htmlspecialchars($task['priority'])); ?></small>
                                        </div>
                                        <div class="task-actions">
                                            <form action="index.php?action=completeTask" method="post" style="display: inline;">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <button type="submit" class="btn btn-warning btn-xs" title="Undo">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                            <form action="index.php?action=deleteTask" method="post" style="display: inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-xs" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Task</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php?action=addTask" method="post">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <select class="form-control" id="priority" name="priority">
                                <option value="high">High Priority</option>
                                <option value="medium" selected>Medium Priority</option>
                                <option value="low">Low Priority</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Task</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Task Modals -->
    <?php foreach ($incompleteTasks as $task): ?>
        <div class="modal fade" id="editTask<?php echo $task['id']; ?>" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Task</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="index.php?action=editTask" method="post">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <div class="form-group">
                                <label for="title<?php echo $task['id']; ?>">Title</label>
                                <input type="text" class="form-control" id="title<?php echo $task['id']; ?>" 
                                       name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="content<?php echo $task['id']; ?>">Content</label>
                                <textarea class="form-control" id="content<?php echo $task['id']; ?>" 
                                          name="content" rows="3"><?php echo htmlspecialchars($task['content']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="priority<?php echo $task['id']; ?>">Priority</label>
                                <select class="form-control" id="priority<?php echo $task['id']; ?>" name="priority">
                                    <option value="high" <?php echo $task['priority'] === 'high' ? 'selected' : ''; ?>>High Priority</option>
                                    <option value="medium" <?php echo $task['priority'] === 'medium' ? 'selected' : ''; ?>>Medium Priority</option>
                                    <option value="low" <?php echo $task['priority'] === 'low' ? 'selected' : ''; ?>>Low Priority</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
