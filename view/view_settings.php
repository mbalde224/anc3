<!DOCTYPE html>
<html>
    <head>
        <link rel="icon" type="image/png" href="images/pizza.png">
        <a href="form/index"><span class="input-group-text bg-dark text-white"><i class="bi bi-arrow-left-short"></i></span></a>
        <meta charset="UTF-8">
        <title>Settings</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-4">
            <!-- Back button -->
            <div class="mb-4">
                <a href="form/index" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-short"></i> Back
                </a>
            </div>

            <h1 class="mb-4">Settings</h1>
            
            <div class="row">
            <p>Hey <b><?= htmlspecialchars($user->get_full_name()) ?></b>!</p>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Profile Settings</h5>
                            <p class="card-text">Update your profile information including name and email.</p>
                            <a href="main/edit_profile" class="btn btn-primary">Edit Profile</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Security</h5>
                            <p class="card-text">Change your account password.</p>
                            <a href="main/change_password" class="btn btn-primary">Change Password</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (isset($_SESSION['message'])) : ?>
                <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['message']); ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
        </div>
    </body>
</html>
