<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="images/pizza.png">
    <base href="<?= $web_root ?>"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($form) ? 'Edit Form' : 'Add New Form' ?></title>
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
        .container {
            max-width: 600px;
            background-color: #282c34;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-primary {
            background-color: #61dafb;
            border: none;
        }
        .btn-primary:hover {
            background-color: #4ba3d9;
        }
        .is-invalid {
            border: 1px solid red;
        }
        .invalid-feedback {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="form/index" class="btn btn-secondary mb-3">← Back</a>
        <h2><?= isset($form) ? 'Edit Form' : 'Create New Form' ?></h2>
        
        <form method="post" action="form/save">
            <input type="hidden" name="id" value="<?= isset($form) ? $form->get_id() : '' ?>">

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= isset($form) ? htmlspecialchars($form->get_title()) : '' ?>" required>
                <?php if (isset($errors['title'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['title'][0]) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control"><?= isset($form) ? htmlspecialchars($form->get_description()) : '' ?></textarea>
            </div>

            <div class="form-check">
                <input type="checkbox" id="is_public" name="is_public" class="form-check-input" <?= isset($form) && $form->is_public() ? 'checked' : '' ?>>
                <label for="is_public" class="form-check-label">Public form</label>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save</button>
        </form>
    </div>
</body>
</html>
