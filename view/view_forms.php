<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="images/pizza.png">
    <title>Forms Manager</title>
    <base href="<?= $web_root ?>"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="form/index">Forms Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if($user->get_role() === Role::GUEST) : ?>   
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="main/logout">Join Us</a>
                        </li>
                    </ul>
                <?php elseif(isset($user)): ?>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="form/index">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="form/create">Add New Form</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="main/settings">Settings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="main/logout">Logout</a>
                        </li>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row" style="max-height: 75vh; overflow-y: auto;">
            <?php foreach($form_data as $data): ?>
                <?php $form = $data['form']; ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($form->get_title()) ?></h5>
                            <?php if (!empty($form->get_description())): ?>
                                <p class="card-text text-muted"><?= htmlspecialchars($form->get_description()) ?></p>
                            <?php else: ?>
                                <p class="card-text text-muted">No description available</p>
                            <?php endif; ?>
                            <p class="text-muted">by <?= htmlspecialchars($form->get_owner()->get_full_name()) ?></p>
                            <p>
                                <?= $form->is_public() 
                                    ? '<span class="badge bg-success">Public</span>' 
                                    : '<span class="badge bg-secondary">Private</span>' ?>
                            </p>
                            <?php if(isset($user) && $user->get_role() !== Role::GUEST && $data['started_time_ago'] !== null): ?>
                                <p><strong>Started:</strong> <?= htmlspecialchars($data['started_time_ago']) ?></p>
                            <?php endif; ?>
                            <?php if ($data['completed_time_ago']) : ?>
                                <p><strong>Completed:</strong> <?= htmlspecialchars($data['status']) ?></p>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between">
                                <?php if ($form->get_questions($form->get_id()) !== []) : ?>
                                    <a href="edit_instance/index/<?= $form->get_id() ?>" class="btn btn-primary btn-sm">Open</a>
                                <?php endif; ?>
                                <?php if(isset($user) && $user->get_role() !== Role::GUEST && $user->can_edit_form($form)): ?>
                                    <a href="question/manage/<?= $form->get_id() ?>" class="btn btn-outline-secondary btn-sm">Manage</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>