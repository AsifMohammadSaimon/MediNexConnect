<?php

include 'config.php';

session_start();

if(isset($_POST['submit'])){

   $name  = clean($_POST['name']);
   $email = clean($_POST['email']);
   $pass  = $_POST['pass'];
   $cpass = $_POST['cpass'];

   // Check passwords match BEFORE hashing
   if($pass !== $cpass){
      $message[] = 'Confirm password does not match!';
   } else {

      $image          = $_FILES['image']['name'];
      $image_size     = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_ext      = strtolower(pathinfo($image, PATHINFO_EXTENSION));
      $allowed_ext    = ['jpg', 'jpeg', 'png'];

      if(!in_array($image_ext, $allowed_ext)){
         $message[] = 'Only JPG, JPEG, and PNG images are allowed!';
      } elseif($image_size > 2000000){
         $message[] = 'Image size is too large! Maximum 2MB allowed.';
      } else {

         $select = $conn->prepare("SELECT id FROM `users` WHERE email = ?");
         $select->execute([$email]);

         if($select->rowCount() > 0){
            $message[] = 'This email is already registered!';
         } else {

            // Use password_hash instead of MD5
            $hashed_pass  = password_hash($pass, PASSWORD_DEFAULT);
            $safe_image   = uniqid() . '_' . basename($image);
            $image_folder = 'uploaded_img/' . $safe_image;

            $insert = $conn->prepare("INSERT INTO `users`(name, email, password, image) VALUES(?,?,?,?)");
            $insert->execute([$name, $email, $hashed_pass, $safe_image]);

            move_uploaded_file($image_tmp_name, $image_folder);
            header('location:login.php');
            exit;
         }
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/components.css">
</head>
<body>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="message">
         <span>'.htmlspecialchars($msg).'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>
   
<section class="form-container">

   <form action="" enctype="multipart/form-data" method="POST">
      <h3>register now</h3>
      <input type="text" name="name" class="box" placeholder="enter your name" required>
      <input type="email" name="email" class="box" placeholder="enter your email" required>
      <input type="password" name="pass" class="box" placeholder="enter your password" required>
      <input type="password" name="cpass" class="box" placeholder="confirm your password" required>
      <input type="file" name="image" class="box" required accept="image/jpg, image/jpeg, image/png">
      <input type="submit" value="register now" class="btn" name="submit">
      <p>already have an account? <a href="login.php">login now</a></p>
   </form>

</section>

</body>
</html>
