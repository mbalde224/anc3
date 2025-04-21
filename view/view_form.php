<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="images/pizza.png">
    <title>Form "<?= $form->get_title() ?>"</title>
    <base href="<?= $web_root ?>"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Djote Forms 🍕</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="main/index">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="form/create">Add new form</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="main/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Form Title and Description -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Form "<?= htmlspecialchars($form->get_title()) ?>" by <?= htmlspecialchars($form->get_owner()->get_full_name()) ?></h1>
                <?php if ($form->get_description() !== null) : ?>     
                    <p class="text-muted"><?= htmlspecialchars($form->get_description()) ?></p>
                <?php endif ?>
                <p>
                    Public: 
                    <?= $form->is_public()
                        ? '<span class="badge bg-success">Enabled</span>'
                        : '<span class="badge bg-secondary">Disabled</span>' ?>
                </p>
            </div>
            <div>
                <?php if ($form->get_owner()->get_id() === $user->get_id()): ?>
                    <a href="form/edit/<?= $form->get_id() ?>" class="btn btn-outline-primary">Edit Form</a>
                    <a href="form/toggle_visibility/<?= $form->get_id() ?>" class="btn btn-outline-secondary">
                        <?= $form->is_public() ? 'Make Private' : 'Make Public' ?>
                    </a>
                    <a href="form/delete/<?= $form->get_id() ?>" class="btn btn-outline-danger" 
                       onclick="return confirm('Are you sure you want to delete this form and all its questions? This action cannot be undone.')">
                        Delete Form
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card shadow-sm">
                <?php if ($instance !== null): ?>
                    <h5 class="card-text">This form is read-only because there are already responses.</h5>
                <?php endif ?>
            </div>
        </div>

        <!-- Questions Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Questions</h3>
            <a href="Add_question/index/<?= $form->get_id() ?>" class="btn btn-primary">Add Question</a>
            <?php if ($form->get_owner()->get_id() === $user->get_id()){?>
            <a href="Manage_shares/index/<?= $form->get_id() ?>" class="btn btn-primary">Manages shares</a>
             <?php } ?>
            <?php if($form->is_Instances()){  ?>
            <a href="instances/index/<?= $form->get_id() ?>" class="btn btn-primary">show instances</a>
            <a href="Analyze/index/<?= $form->get_id() ?>" class="btn btn-primary">Analyze</a>

            <?php  }?>

        </div>

        <div class="row gy-3">
            <?php foreach ($questions as $question): ?>
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($question->get_title()) ?></h5>
                            <?php if ($question->get_description() !== null ) : ?>
                                <p class="card-text fst-italic"><?= htmlspecialchars($question->get_description()) ?></p>
                            <?php endif ?>
                            <p class="mb-1"><strong>Type:</strong> <?= htmlspecialchars($question->get_type()) ?></p>
                            <p class="mb-1">
                                <strong>Required:</strong> <?= $question->is_required()
                                    ? '<span class="badge bg-success">True</span>'
                                    : '<span class="badge bg-secondary">False</span>' ?>
                            </p>
                            <!-- Action Buttons -->
                            <?php if ($instance === null): ?>
                            <div class="d-flex justify-content-between mt-3">
                                <div>
                                    <a href="question/move_up/<?= $question->get_id() ?>" class="btn btn-outline-primary btn-sm">⬆</a>
                                    <a href="question/move_down/<?= $question->get_id() ?>" class="btn btn-outline-primary btn-sm">⬇</a>
                                </div>
                                <div>
                                    <a href="question/add/<?= $form->get_id()?>/<?= $question->get_id() ?>" class="btn btn-outline-secondary btn-sm">✏️</a>
                                    <a href="question/delete/<?= $question->get_id() ?>" class="btn btn-outline-danger btn-sm">🗑️</a>
                                </div>
                            </div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
