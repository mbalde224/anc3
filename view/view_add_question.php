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
    <form class="" action='Add_question/add' method="post">
  <div class="form-group">
    <label for="exampleFormControlInput1">title</label>
    <input type="text" class="form-control" id="exampleFormControlInput1" name = "title">
  </div>
  <div class="form-group">
    <label for="exampleFormControlTextarea1">Description</label>
    <textarea class="form-control" name="description" id="exampleFormControlTextarea1" rows="3"></textarea>
  </div>
  <span>Type</span>
  <br>

  <select class="custom-select" id="inputGroupSelect01" name="selected_type">
  <option selected>--Select à type--</option>
                <option value="short">short</option>
                <option value="long">long</option>
                <option value="date">date</option>
                <option value="email">email</option>
  </select>

  <div class="form-check">
    <input type="checkbox" class="form-check-input" name="required" id="exampleCheck1">
    <label class="form-check-label" for="exampleCheck1">Required question</label>
  </div>
  <input  type="hidden" name="form_id" value="<?php echo htmlspecialchars($form->get_id());?>" >
    <?php if ($message !="ok") {?>
        <span>aaa<?php echo htmlspecialchars($message);?></span>
        <?php } ?>
  <button  type="submit" class="btn btn-primary">Submit</button>

</form>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
