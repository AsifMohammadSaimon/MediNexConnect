<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit;
};

if(isset($_POST['update_product'])){

   $pid      = (int)$_POST['pid'];
   $name     = clean($_POST['name']);
   $price    = (int)$_POST['price'];
   $category = clean($_POST['category']);
   $details  = clean($_POST['details']);
   $old_image = clean($_POST['old_image']);

   $update_product = $conn->prepare("UPDATE `products` SET name = ?, category = ?, details = ?, price = ? WHERE id = ?");
   $update_product->execute([$name, $category, $details, $price, $pid]);

   $message[] = 'Product updated successfully!';

   if(!empty($_FILES['image']['name'])){
      $image          = $_FILES['image']['name'];
      $image_size     = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_ext      = strtolower(pathinfo($image, PATHINFO_EXTENSION));
      $allowed_ext    = ['jpg', 'jpeg', 'png'];

      if(!in_array($image_ext, $allowed_ext)){
         $message[] = 'Only JPG, JPEG, and PNG images are allowed!';
      } elseif($image_size > 2000000){
         $message[] = 'Image size is too large! Maximum 2MB.';
      } else {
         $safe_image   = uniqid() . '_' . basename($image);
         $image_folder = 'uploaded_img/' . $safe_image;

         $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
         $update_image->execute([$safe_image, $pid]);

         move_uploaded_file($image_tmp_name, $image_folder);

         // Only delete old image if it exists and is not a default
         if($old_image && file_exists('uploaded_img/'.$old_image)){
            unlink('uploaded_img/'.$old_image);
         }
         $message[] = 'Image updated successfully!';
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
   <title>Update Product</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '<div class="message"><span>'.htmlspecialchars($msg).'</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
   }
}
?>

<section class="update-product">

   <h1 class="title">update product</h1>   

   <?php
      if(!isset($_GET['update'])){
         echo '<p class="empty">No product selected!</p>';
      } else {
         $update_id = (int)$_GET['update'];
         $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
         $select_products->execute([$update_id]);
         if($select_products->rowCount() > 0){
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= htmlspecialchars($fetch_products['image']); ?>">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" alt="">
      <input type="text" name="name" placeholder="enter product name" required class="box" value="<?= htmlspecialchars($fetch_products['name']); ?>">
      <input type="number" name="price" min="0" placeholder="enter product price" required class="box" value="<?= $fetch_products['price']; ?>">
      <select name="category" class="box" required>
         <option selected><?= htmlspecialchars($fetch_products['category']); ?></option>
         <option value="OTC Medicine">OTC Medicine</option>
         <option value="Dental Care">Dental Care</option>
         <option value="Baby Care">Baby Care</option>
         <option value="Skin Care">Skin Care</option>
         <option value="Health Care">Health Care</option>
      </select>
      <textarea name="details" required placeholder="enter product details" class="box" cols="30" rows="10"><?= htmlspecialchars($fetch_products['details']); ?></textarea>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      <div class="flex-btn">
         <input type="submit" class="btn" value="update product" name="update_product">
         <a href="admin_products.php" class="option-btn">go back</a>
      </div>
   </form>
   <?php
            }
         }else{
            echo '<p class="empty">No product found!</p>';
         }
      }
   ?>

</section>

<script src="js/script.js"></script>

</body>
</html>
