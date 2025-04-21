<!DOCTYPE html>
<html lang="en">
<head>
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

      <h1>Submitted Instance(s) of form "<?php echo htmlspecialchars($form->get_title()); ?>":</h1>
    
            <form class='' action='Instances/delete' method='post' >

                <?php $i =0;
                foreach($listes_instance as $Instance){
    
                ?>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" name="ids[]" value="<?php echo htmlspecialchars($Instance->get_id()); ?>">
                <label class="form-check-label" for="ids">
                    <?php echo htmlspecialchars($Instance->get_started()->format('d-m-Y H:i:s'));?>
                    <br>
                    Answered by <?php echo htmlspecialchars($liste_Users[$i]->get_full_name());?>
                     </label>
                </div>
                <?php 
                } ?>

                <div class="d-flex justify-content-center mt-4">

                <button class="btn btn-danger" type="submit"name="form_id" value="<?php echo htmlspecialchars($form->get_id()); ?>">Delete selected</button>
                </div>
            
            </form>
        
        
    </div>
    
        
      

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
