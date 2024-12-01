<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Login</h2>
                        
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

                        <form action="index.php?action=login" method="post">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>
                        <p class="text-center mt-3">Don't have an account? <a href="index.php?action=register">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
