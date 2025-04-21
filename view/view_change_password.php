<!DOCTYPE html>
<html>
    <head>
        <link rel="icon" type="image/png" href="images/pizza.png">
        <meta charset="UTF-8">
        <title>Change Password</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background-color: #20232a;
                color: #ffffff;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
            }
            .password-container {
                background-color: #282c34;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
                text-align: center;
                width: 100%;
                max-width: 400px;
            }
            .btn-primary {
                background-color: #61dafb;
                border: none;
            }
            .btn-primary:hover {
                background-color: #4ba3d9;
            }
            .btn-secondary {
                background-color: #444;
                border: none;
            }
            .btn-secondary:hover {
                background-color: #555;
            }
        </style>
    </head>
    <body>
        <div class="password-container">
            <h1 class="mb-4">Change Password</h1>
            
            <form action="main/change_password" method="post" novalidate>
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" 
                           name="current_password" required>
                </div>
                
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" 
                           name="new_password" required>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" 
                           name="confirm_password" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Change Password</button>
                    <a href="main/settings" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
            
            <?php if (count($errors) != 0) : ?>
                <div class="alert alert-danger mt-3">
                    <p>Please correct the following errors:</p>
                    <ul class="mb-0">
                        <?php foreach($errors as $error) : ?>
                            <li><?= $error ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html> 