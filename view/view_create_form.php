<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="images/pizza.png">
    <base href="<?= $web_root ?>"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Djote Forms 🍕</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Add new form</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="main/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4 text-center">Create a New Form</h1>
        <form action="form/create" method="post" class="card shadow-sm p-4">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-control" placeholder="Enter a title" value="<?= htmlspecialchars($_POST['title'] ?? '')?>"required>
                <?php if (count($errors['title']) != 0) : ?>
                    <div class="text-danger mt-1">
                        <ul class="mb-0">
                            <?php foreach($errors['title'] as $error) : ?> 
                                <li><?= $error ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter a description"><?= htmlspecialchars($_POST['description'] ?? '')?></textarea>
                <?php if (count($errors['description']) != 0) : ?>
                    <div class="text-danger mt-1">
                        <ul class="mb-0">
                            <?php foreach ($errors['description'] as $error) : ?>
                                <li><?= $error ?><li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" id="is_public" name="is_public" class="form-check-input" <?= isset($_POST['is_public']) ? 'checked' : ''?>>
                <label for="is_public" class="form-check-label">Public Form</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Create Form</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
