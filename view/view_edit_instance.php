<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="images/pizza.png">
    <base href="<?= $web_root ?>"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>?</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .wrapper {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-auto-rows: minmax(auto, auto);
        }
        .card-text {
            grid-column: 1/2;
            grid-row: auto;
        }
        .card-text-2 {
            grid-column: 2/3;
            grid-row: auto;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="container mt-5">

        <!-- MESSAGE SI DEJA SOUMIS -->
        <?php if ($instance->is_completed() && $user !== 'guest'): ?>
            <div class="alert alert-warning">
                <p>You have already answered this form.</p>
                <p>You can view your submission or submit again.</p>
                <p>What would you like to do ?</p>
                <form action="edit_instance/edit" method="post">
                    <input type="hidden" name="form_id" value="<?= $form->get_id() ?>">
                    <button type="submit" name="action" value="view" class="btn btn-outline-light">
                        View Submitted Instance
                    </button>
                    <button type="submit" name="action" action="start_new_instance" value="new" class="btn btn-primary">
                        Submit again
                    </button>
                    <a href="main/index" name="action" value="new" class="btn btn-primary">
                        Cancel
                    </a>
                </form>
            </div>

        <!-- TO DO : Answer the form avec le titre, description, started et in progress -->

        <!-- QUESTIONS -->
        <?php else: ?>
            <h5 class="card-title">Answer the form</h5><br>
            <div class="wrapper">
                <p class="card-text">Title :</p>
                <p class="card-text-2"><strong><?= htmlspecialchars($form->get_title()) ?></strong></p>
                <p class="card-text">Description :</p>
                <p class="card-text-2"><strong><?= htmlspecialchars($form->get_description() ?? 'No description.') ?></strong></p>
                <p class="card-text">Started :</p>
                <p class="card-text-2"><strong><?= htmlspecialchars($data['started_time_ago']) ?></strong></p>
                <?php if ($is_read_only): ?>
                    <p class="card-text">Completed :</p>
                    <p class="card-text-2"><strong><?= htmlspecialchars($data['started_time_ago']) ?></strong></p>
                <?php endif ?>
            </div>
                <p class="card-text"><?=$is_read_only ? "" : "In progress..."?></p><br>
                <h5 class="card-title">Question <?= htmlspecialchars($current_question->get_idx()) ?> / <?= htmlspecialchars(count($questions))?></h5><br>
                <h5 class="card-title"><?= htmlspecialchars($current_question->get_title()) ?>
                <?php if ($current_question->is_required()): ?>
                    <span class="card-title" style="color: red;">(*)</span>
                <?php endif; ?></h5>
                
                <form action="edit_instance/submit" method="post">
                    <input type="hidden" name="instance_id" value="<?= $instance->get_id() ?>">
                    <input type="hidden" name="question_id" value="<?= $current_question->get_id() ?>">

                    <div class="mb-3">                     
                        <label for="question_<?= $current_question->get_id() ?>" class="form-label">
                            <?= htmlspecialchars($current_question->get_description() ?? "No description") ?>
                        </label>
                        <?php if ($is_read_only): ?>
                            <input class="form-control" type="text"
                                value= <?= htmlspecialchars($answers[$current_question->get_id()] ?? "No answer provided") ?>
                            aria-label="readonly input example" readonly>
                        <?php else: ?>
                            <?php if ($current_question->get_type() === 'short'): ?>
                                <input type="text" class="form-control" id="question_<?= $current_question->get_id() ?>" name="answer_value" value="<?= htmlspecialchars($answer ? $answer->get_value() : '') ?>">
                            <?php elseif ($current_question->get_type() === 'long'): ?>
                                <textarea class="form-control" id="question_<?= $current_question->get_id() ?>" name="answer_value"><?= htmlspecialchars($answer ? $answer->get_value() : '') ?></textarea>
                            <?php elseif ($current_question->get_type() === 'date'): ?>
                                <input type="date" class="form-control" id="question_<?= $current_question->get_id() ?>" name="answer_value" value="<?= htmlspecialchars($answer ? $answer->get_value() : '') ?>">
                            <?php elseif ($current_question->get_type() === 'email'): ?>
                                <input type="email" class="form-control" id="question_<?= $current_question->get_id() ?>" name="answer_value" value="<?= htmlspecialchars($answer ? $answer->get_value() : '') ?>">
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

        <?php endif; ?>
    </div>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

                <!-- Navigation buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <?php if (!$is_read_only):?>
                        <button type="submit" name="action" value="save_cancel" class="btn btn-secondary">Save and Cancel</button>
                    <?php endif ?>
                    <?php if ($prev_idx !== null): ?>
                        <button type="submit" name="action" value="previous" class="btn btn-secondary">Previous</button>
                    <?php endif; ?>
                    <?php if ($next_idx !== null): ?>
                        <button type="submit" name="action" value="next" class="btn btn-primary">Next</button>
                    <?php elseif (!$is_read_only): ?>
                        <button type="submit" name="action" value="submit" class="btn btn-success">Submit</button>
                    <?php endif; ?>
                </div>
            </form>
    </div>
</body>
</html>
