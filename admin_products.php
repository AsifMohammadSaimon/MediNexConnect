<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit;
};

if(isset($_POST['add_product'])){

   $name     = clean($_POST['name']);
   $price    = (int)$_POST['price'];
   $category = clean($_POST['category']);
   $details  = clean($_POST['details']);

   $image          = $_FILES['image']['name'];
   $image_size     = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_ext      = strtolower(pathinfo($image, PATHINFO_EXTENSION));
   $allowed_ext    = ['jpg', 'jpeg', 'png'];

   if(!in_array($image_ext, $allowed_ext)){
      $message[] = 'Only JPG, JPEG, and PNG images are allowed!';
   } elseif($image_size > 2000000){
      // FIX: check image size BEFORE inserting into DB
      $message[] = 'Image size is too large! Maximum 2MB.';
   } else {

      $select_products = $conn->prepare("SELECT id FROM `products` WHERE name = ?");
      $select_products->execute([$name]);

      if($select_products->rowCount() > 0){
         $message[] = 'Product name already exists!';
      } else {
         $safe_image   = uniqid() . '_' . basename($image);
         $image_folder = 'uploaded_img/' . $safe_image;

         $insert_products = $conn->prepare("INSERT INTO `products`(name, category, details, price, image) VALUES(?,?,?,?,?)");
         $insert_products->execute([$name, $category, $details, $price, $safe_image]);

         move_uploaded_file($image_tmp_name, $image_folder);
         $message[] = 'New product added successfully!';
      }
   }
};

if(isset($_GET['delete'])){
   $delete_id = (int)$_GET['delete'];
   $select_delete_image = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
   $select_delete_image->execute([$delete_id]);
   $fetch_delete_image = $select_delete_image->fetch(PDO::FETCH_ASSOC);

   if($fetch_delete_image && file_exists('uploaded_img/'.$fetch_delete_image['image'])){
      unlink('uploaded_img/'.$fetch_delete_image['image']);
   }

   $delete_products = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_products->execute([$delete_id]);
   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   header('location:admin_products.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>
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

<section class="add-products">

   <h1 class="title">add new product</h1>

   <form action="" method="POST" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
         <input type="text" name="name" class="box" required placeholder="enter product name">
         <select name="category" class="box" required>
            <option value="" selected disabled>select category</option>
               <option value="OTC Medicine">OTC Medicine</option>
               <option value="Dental Care">Dental Care</option>
               <option value="Baby Care">Baby Care</option>
               <option value="Skin Care">Skin Care</option>
               <option value="Health Care">Health Care</option>
         </select>
         </div>
         <div class="inputBox">
         <input type="number" min="0" name="price" class="box" required placeholder="enter product price">
         <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
         </div>
      </div>
      <textarea name="details" class="box" required placeholder="enter product details" cols="30" rows="10"></textarea>
      <input type="submit" class="btn" value="add product" name="add_product">
   </form>

</section>

<section class="show-products">

   <h1 class="title">products added</h1>

   <div class="box-container">

   <?php
      $show_products = $conn->prepare("SELECT * FROM `products`");
      $show_products->execute();
      if($show_products->rowCount() > 0){
         while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
   ?>
   <div class="box">
      <div class="price">৳<?= $fetch_products['price']; ?>/-</div>
      <img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" alt="">
      <div class="name"><?= htmlspecialchars($fetch_products['name']); ?></div>
      <div class="cat"><?= htmlspecialchars($fetch_products['category']); ?></div>
      <div class="details"><?= htmlspecialchars($fetch_products['details']); ?></div>
      <div class="flex-btn">
         <a href="admin_update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">update</a>
         <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">delete</a>
      </div>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">No products added yet!</p>';
   }
   ?>

   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>
