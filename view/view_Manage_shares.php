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

        <p>Shares :</p>
        
        <h1>🍕 Djote Forms</h1>
        <?php
        foreach($users as $user){
        ?>
        <!-- sans 2 bouton a la fin <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button">Button</button>
            </div>
        </div> <img src="moins.png" alt="icone" style="position:relative;" /> -->
        <form class='' action='User_form_accesses/reverse' method='post' >
        <div class="input-group">
            <span class="input-group-text" id=""><?php echo htmlspecialchars($user->get_full_name()); ?>     (<?php echo  htmlspecialchars($form->get_user_access_type($user->get_id())); ?>)</span>  <div class="input-group-append">
            <div class="input-group-append d-flex align-items-center">
           
                <input type='text' name='userId' value='<?= $user->get_id() ?>' hidden>
                <input type='text' name='formId' value='<?= $form->get_id() ?>' hidden>
                <button class="btn btn-outline-secondary" type="submit" action =><img src="monde.png" alt="icone" style="position:relative;" /></button>
            </form>
            <form class='' action='User_form_accesses/delete' method='post' >
                <input type='text' name='userId' value='<?= $user->get_id() ?>' hidden>
                <input type='text' name='formId' value='<?= $form->get_id() ?>' hidden>
               <!-- <input type="image" id="image" alt="Login" src="moins.png" />-->
               <button class="btn btn-outline-secondary" type="submit"><img src="moins.png" alt="icone" style="position:relative;" /></button>
            </form>
            </div>
        </div>
</div>
<?php
}
?>
      
        
        
        <form class='' action='User_form_accesses/add_User' method='post' >
        <div class="input-group mb-3">
            <select class="custom-select" id="inputGroupSelect01" name="selected_user" >
                <option selected>User</option>
                <?php
                foreach($simple_Users as $user){
                ?>
                        <?php $faut_til_ajouter =1;?>
                    <?php if ($user["id"] != $form->get_owner()->get_id()) {?>
                        <?php
                       

                        foreach($users as $user2){
                        ?>
                            <?php if ($user["id"] == $user2->get_id()) {?>
                                <?php $faut_til_ajouter = 0;?>
                            <?php
                            } ?>
                        <?php
                            }
                        ?>
                        <?php echo htmlspecialchars(print_r($simple_Users));?>
                        <?php if ($faut_til_ajouter==1) {?>
                            <option value="<?php echo htmlspecialchars($user["id"]); ?>"><?php echo htmlspecialchars($user["full_name"]); ?></option>
                            
                        <?php
                         }
                        ?>
                    <?php
                     } ?>
                <?php
                } ?>
            
            </select>
            <select class="custom-select" id="inputGroupSelect01" name="selected_user2">
            <?php if ($form->is_public()==0) {
            ?>
            <option selected>-Permission-</option>
                <option value="editor">editor</option>
                <option value="user">user</option>
                
            <?php
            }
            ?>
            <?php if ($form->is_public()==1) {?>
            <option value="editor" selected >editor</option>
            <?php }?>
            </select>
            <input type='text' name='formId' value='<?= $form->get_id() ?>' hidden>
            <button class="btn btn-outline-secondary" type="submit"><img src="plus.png" alt="icone" style="position:relative;" /></button>
        </form>
        </div>
    
        </div>
        
      

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
