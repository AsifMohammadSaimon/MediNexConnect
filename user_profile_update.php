<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
   exit;
};

// FIX: fetch the user profile first (was missing entirely — caused $fetch_profile undefined)
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if(!$fetch_profile){
   header('location:logout.php');
   exit;
}

if(isset($_POST['update_profile'])){

   $name  = clean($_POST['name']);
   $email = clean($_POST['email']);

   $update_profile = $conn->prepare("UPDATE `users` SET name = ?, email = ? WHERE id = ?");
   $update_profile->execute([$name, $email, $user_id]);
   $message[] = 'Profile updated successfully!';

   // Handle image upload
   if(!empty($_FILES['image']['name'])){
      $image          = $_FILES['image']['name'];
      $image_size     = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_ext      = strtolower(pathinfo($image, PATHINFO_EXTENSION));
      $old_image      = clean($_POST['old_image']);
      $allowed_ext    = ['jpg', 'jpeg', 'png'];

      if(!in_array($image_ext, $allowed_ext)){
         $message[] = 'Only JPG, JPEG, and PNG images are allowed!';
      } elseif($image_size > 2000000){
         $message[] = 'Image size is too large! Maximum 2MB.';
      } else {
         $safe_image   = uniqid() . '_' . basename($image);
         $image_folder = 'uploaded_img/' . $safe_image;

         $update_image = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
         $update_image->execute([$safe_image, $user_id]);

         move_uploaded_file($image_tmp_name, $image_folder);

         if($old_image && file_exists('uploaded_img/'.$old_image)){
            unlink('uploaded_img/'.$old_image);
         }
         $message[] = 'Profile picture updated!';

         // Refresh profile data to show new image
         $fetch_profile['image'] = $safe_image;
      }
   }

   // Handle password change
   $entered_old = $_POST['update_pass'];
   $new_pass    = $_POST['new_pass'];
   $confirm_pass= $_POST['confirm_pass'];

   if(!empty($entered_old) && !empty($new_pass) && !empty($confirm_pass)){

      // Verify old password (supports both MD5 legacy and bcrypt)
      $stored = $fetch_profile['password'];
      if(strlen($stored) === 32){
         $old_valid = (md5($entered_old) === $stored);
      } else {
         $old_valid = password_verify($entered_old, $stored);
      }

      if(!$old_valid){
         $message[] = 'Old password is incorrect!';
      } elseif($new_pass !== $confirm_pass){
         $message[] = 'New passwords do not match!';
      } else {
         $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
         $update_pass_query = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_pass_query->execute([$hashed, $user_id]);
         $message[] = 'Password updated successfully!';
         $fetch_profile['password'] = $hashed;
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
   <title>Update Profile</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/components.css">
</head>
<body>
   
<?php include 'header.php'; ?>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '<div class="message"><span>'.htmlspecialchars($msg).'</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
   }
}
?>

<section class="update-profile">

   <h1 class="title">update profile</h1>

   <form action="" method="POST" enctype="multipart/form-data">
      <img src="uploaded_img/<?= htmlspecialchars($fetch_profile['image']); ?>" alt="">
      <div class="flex">
         <div class="inputBox">
            <span>username :</span>
            <input type="text" name="name" value="<?= htmlspecialchars($fetch_profile['name']); ?>" placeholder="update username" required class="box">
            <span>email :</span>
            <input type="email" name="email" value="<?= htmlspecialchars($fetch_profile['email']); ?>" placeholder="update email" required class="box">
            <span>update pic :</span>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
            <input type="hidden" name="old_image" value="<?= htmlspecialchars($fetch_profile['image']); ?>">
         </div>
         <div class="inputBox">
            <!-- FIX: removed old_pass hidden field that exposed hashed password to client -->
            <span>old password :</span>
            <input type="password" name="update_pass" placeholder="enter previous password" class="box">
            <span>new password :</span>
            <input type="password" name="new_pass" placeholder="enter new password" class="box">
            <span>confirm password :</span>
            <input type="password" name="confirm_pass" placeholder="confirm new password" class="box">
         </div>
      </div>
      <div class="flex-btn">
         <input type="submit" class="btn" value="update profile" name="update_profile">
         <a href="home.php" class="option-btn">go back</a>
      </div>
   </form>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
