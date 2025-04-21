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
            /*justify-content: center;*/
            /*align-items: center;*/
            min-height: 100vh;
            margin: 0;
        }
        /* Commentaire .login-container {
            background-color: #282c34;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }*/
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
    <div>

      <h1>Statistics of form "<?php echo htmlspecialchars($form->get_title()); ?>":</h1>
        
        
        <form class='' action='Analyze/show_statistic' method='post' >
        <div class="input-group mb-3">
            <select class="custom-select" id="inputGroupSelect01" name="questionId" >
            <?php
                if ($QuestionId =='-Question-') {
                ?>
                <option value="-Question-" selected>-Question-</option>
                <?php
                } ?>

                <?php
                if ($QuestionId != '-Question-') {
                ?>
                <option value="<?php echo htmlspecialchars($QuestionId); ?>" selected><?php echo htmlspecialchars($titre_current); ?></option>
                <?php
                } ?>


                <?php
                foreach($questions as $question){
                ?>

                    <option value="<?php echo htmlspecialchars($question->get_id()); ?>"><?php echo htmlspecialchars($question->get_title()); ?></option>
                            
                <?php
                } ?>
            
            </select>
            
            <input type='text' name='formId' value='<?= $form->get_id() ?>' hidden>
            <button class="btn btn-outline-secondary" type="submit"><img src="plus.png" alt="icone" style="position:relative;" /></button>
           
        </div>
        </form>
        <div>

            <BR>
            <br>
            <?php
                if ($QuestionId !='-Question-') {
                ?>
                <table class="table table-sm">
                <thead>
                    <tr>
                    <th scope="col">value</th>
                    <th scope="col">count</th>
                    <th scope="col">ratio</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($liste_reponse as $element => $count) {                ?>

                    <tr>
                    <th scope="row"><?php echo htmlspecialchars($element); ?></th>
                    <td><?php echo htmlspecialchars($count); ?></td>
                    <td><?php echo htmlspecialchars(round(($count / $nombre_elem)*100)); ?></td>
                    </tr>                            
                <?php
                } ?>
                    
                
                </tbody>
                </table>                <?php
                } ?>
           
        </div>
        
    </div>
    
        
      

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
