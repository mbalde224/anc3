<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="images/pizza.png">

    <base href="<?= $web_root ?>"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
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
        .login-container {
            background-color: #282c34;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .login-container h1 {
            font-size: 24px;
            color: #61dafb;
            margin-bottom: 20px;
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
        .debug-info h3 {
            color: #ffc107;
        }
        a {
            color: #61dafb;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>🍕 Djote Forms</h1>
        <form class="needs-validation" action="main/login" method="post" novalidate>
            <h2>Sign in</h2>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $email ?>" placeholder="Email" required>
                <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" value="<?= $password ?>" placeholder="Password" required>
                <div class="invalid-feedback">Password is required.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">Log in</button>
            <!--<button type="button" class="btn btn-secondary w-100"onclick="window.location.href='index.php?controller=main&action=guest_login'">Continue as guest</button>-->
            <a href="index.php?controller=main&action=guest_login" class="btn btn-primary w-100 mb-2">Continue as guest</a>
            <p class="mt-3">New here? <a href="main/signup">Click here to subscribe!</a></p>
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

        <div class="debug-info mt-4 text-start">
            <h3>For Debug Purpose</h3>
            <ul>
                <?php if (Configuration::is_dev()): ?>
                    <li>Login as: <a href="index.php?controller=main&action=login_as&email=bepenelle@epfc.eu">bepenelle@epfc.eu</a></li>
                    <li>Login as: <a href="index.php?controller=main&action=login_as&email=boverhaegen@epfc.eu">boverhaegen@epfc.eu</a></li>
                    <li>Login as: <a href="index.php?controller=main&action=login_as&email=mamichel@epfc.eu">mamichel@epfc.eu</a></li>
                    <li>Login as: <a href="index.php?controller=main&action=login_as&email=xapigeolet@epfc.eu">xapigeolet@epfc.eu</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
