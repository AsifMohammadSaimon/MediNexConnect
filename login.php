<?php

include 'config.php';

session_start();

if(isset($_POST['submit'])){

   $email = clean($_POST['email']);
   $pass  = $_POST['pass'];

   $sql  = "SELECT * FROM `users` WHERE email = ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$email]);

   if($stmt->rowCount() > 0){

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // Support both old MD5 passwords and new password_hash passwords
      $password_valid = false;
      if(strlen($row['password']) === 32){
         // Old MD5 password — compare and optionally upgrade
         $password_valid = (md5($pass) === $row['password']);
         if($password_valid){
            // Upgrade to bcrypt on next login
            $new_hash = password_hash($pass, PASSWORD_DEFAULT);
            $upgrade  = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $upgrade->execute([$new_hash, $row['id']]);
         }
      } else {
         $password_valid = password_verify($pass, $row['password']);
      }

      if($password_valid){
         if($row['user_type'] == 'admin'){
            $_SESSION['admin_id'] = $row['id'];
            header('location:admin_page.php');
            exit;
         } elseif($row['user_type'] == 'user'){
            $_SESSION['user_id'] = $row['id'];
            header('location:home.php');
            exit;
         }
      } else {
         $message[] = 'Incorrect email or password!';
      }

   } else {
      $message[] = 'Incorrect email or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
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

   <form action="" method="POST">
      <h3>login now</h3>
      <input type="email" name="email" class="box" placeholder="enter your email" required>
      <input type="password" name="pass" class="box" placeholder="enter your password" required>
      <input type="submit" value="login now" class="btn" name="submit">
      <p>don't have an account? <a href="register.php">register now</a></p>
   </form>

</section>

</body>
</html>
