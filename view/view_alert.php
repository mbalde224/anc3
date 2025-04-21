<!DOCTYPE html>
<html lang="en">
<head>

    <link rel="stylesheet" href="/css/stylesheet.css">
    <link rel="icon" type="image/png" href="images/pizza.png">

    <base href="<?= $web_root ?>"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert</title>
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
        .bi {
            font-size: 3em !important;
        }
        button.btn {
            display: inline-block !important;
        }
        

    </style>
</head>
<body>
    <div class="signup-container" style="width: 100%; max-width: 400px; margin: 0 auto; padding: 20px; background-color: #282c34; border-radius: 10px; text-align: center;">
    <i class="bi <?= htmlspecialchars($icon) ?>" style="color : <?= htmlspecialchars($icon_color) ?>; font-size: 24px;"></i>
    <h2 style="color : <?= htmlspecialchars($text_color) ?>; margin-top: 10px;"><?= htmlspecialchars($title) ?></h2>
    <hr style="border-color: <?= htmlspecialchars($text_color) ?>;">
    <span class="card-title" style="color: <?= htmlspecialchars($color) ?>;">
        <?= $description ?>
    </span>
    <br>
    <?php foreach ($buttons as $button): ?>
        <form class="needs-validation" action="<?= htmlspecialchars($button['action']) ?>" method="<?= htmlspecialchars($button['method']) ?>">
            <button type="submit" class="<?= htmlspecialchars($button['class']) ?>">
                <?= htmlspecialchars($button['label']) ?>
            </button>
        </form>
    <?php endforeach; ?>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
