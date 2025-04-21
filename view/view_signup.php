<!DOCTYPE html>
<html lang="en">
<head>

    <link rel="stylesheet" href="/css/stylesheet.css">
    <link rel="icon" type="image/png" href="images/pizza.png">

    <base href="<?= $web_root ?>"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
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
        .form-control {
            color: lightgrey !important;
            border-color: lightgrey !important;
            background-color: #20232a;
            color: white;
        }
        .input-group-text {
            background-color: #282c34;
        }
        .form-control:focus {
            border-color: lightgrey !important;
            background-color: #20232a;
            color: white;
        }
        .signup-container {
            background-color: #282c34;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .signup-container h1 {
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
        a {
            color: #61dafb;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .form-control::placeholder {
            color: #aaa !important; /* Couleur gris clair pour le placeholder */
            opacity: 1; /* Assure la pleine opacité pour éviter un effet transparent */
        }

    </style>
</head>
<body>
    <div class="signup-container">

        <form class="needs-validation" action="main/signup" method="post" novalidate>
            <h2>Sign up</h2><hr>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-dark text-white"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $email ?>" placeholder="Email" required>
                    <div class="invalid-feedback">Please enter a valid email.</div>
                </div>    
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-dark text-white"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?= $full_name ?>" placeholder="Full Name" required>
                    <div class="invalid-feedback">Please enter your full name.</div>
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-dark text-white"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" value="<?= $password ?>" placeholder="Password" required>
                    <div class="invalid-feedback">Password is required.</div>
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-dark text-white"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                    <div class="invalid-feedback">Please confirm your password.</div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">Sign Up</button>
            <a href="main/login" class="btn btn-primary w-100 mb-2-1">Cancel</button></a>
            <p class="mt-3">Already have an account? <a href="main/login">Log in here!</a></p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
