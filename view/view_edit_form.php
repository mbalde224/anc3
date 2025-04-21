<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="images/pizza.png">
    <meta charset="UTF-8">
    <title>Edit Form</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <h1>Edit Form</h1>
        <form action="form/edit/<?= $form->get_id() ?>" method="post" novalidate>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($description ?? '') ?></textarea>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_public" name="is_public" <?= $is_public ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_public">Public</label>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="form/index" class="btn btn-secondary">Cancel</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 