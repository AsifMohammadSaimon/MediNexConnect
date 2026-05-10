<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
   exit;
};

if(isset($_POST['order'])){

   $name    = clean($_POST['name']);
   $number  = clean($_POST['number']);
   $email   = clean($_POST['email']);
   $method  = clean($_POST['method']);
   $address = 'flat no. '. clean($_POST['flat']) .' '. clean($_POST['street']) .' '. clean($_POST['city']) .' '. clean($_POST['country']) .' - '. clean($_POST['pin_code']);
   $placed_on = date('d-M-Y');

   $cart_total    = 0;
   $cart_products = [];

   $cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $cart_query->execute([$user_id]);
   if($cart_query->rowCount() > 0){
      while($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)){
         $cart_products[] = $cart_item['name'].' ( '.$cart_item['quantity'].' )';
         $cart_total += ($cart_item['price'] * $cart_item['quantity']);
      };
   };

   if($cart_total == 0){
      $message[] = 'Your cart is empty!';
   } else {
      $total_products = implode(', ', $cart_products);

      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES(?,?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on]);

      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);
      $message[] = 'Order placed successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
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

<section class="display-orders">

   <?php
      $cart_grand_total = 0;
      $select_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart_items->execute([$user_id]);
      if($select_cart_items->rowCount() > 0){
         while($fetch_cart_items = $select_cart_items->fetch(PDO::FETCH_ASSOC)){
            $cart_total_price = ($fetch_cart_items['price'] * $fetch_cart_items['quantity']);
            $cart_grand_total += $cart_total_price;
   ?>
   <p> <?= htmlspecialchars($fetch_cart_items['name']); ?> <span>(<?= '৳'.$fetch_cart_items['price'].'/- x '. $fetch_cart_items['quantity']; ?>)</span> </p>
   <?php
       }
      }else{
         echo '<p class="empty">your cart is empty!</p>';
      }
   ?>
   <div class="grand-total">Total : <span>৳<?= $cart_grand_total; ?>/-</span></div>
</section>

<section class="checkout-orders">

   <form action="" method="POST">

      <h3>Place your order</h3>

      <div class="flex">
         <div class="inputBox">
            <span>Your name :</span>
            <input type="text" name="name" placeholder="enter your name" class="box" required>
         </div>
         <div class="inputBox">
            <span>Your Phone Number :</span>
            <input type="number" name="number" placeholder="enter your number" class="box" required>
         </div>
         <div class="inputBox">
            <span>Your Email :</span>
            <input type="email" name="email" placeholder="enter your email" class="box" required>
         </div>
         <div class="inputBox">
            <span>Payment Method :</span>
            <!-- FIX: values now match their labels correctly -->
            <select name="method" class="box" required>
               <option value="cash on delivery">Cash On Delivery</option>
               <option value="bkash">Bkash</option>
               <option value="nagad">Nagad</option>
               <option value="credit card">Credit Card</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Address line 01 :</span>
            <input type="text" name="flat" placeholder="e.g. flat number" class="box" required>
         </div>
         <div class="inputBox">
            <span>Address line 02 :</span>
            <input type="text" name="street" placeholder="e.g. street name" class="box" required>
         </div>
         <div class="inputBox">
            <span>City :</span>
            <input type="text" name="city" placeholder="e.g. your city" class="box" required>
         </div>
         <div class="inputBox">
            <span>Country :</span>
            <input type="text" name="country" placeholder="e.g. your country" class="box" required>
         </div>
         <div class="inputBox">
            <span>Pin Code :</span>
            <input type="number" min="0" name="pin_code" placeholder="e.g. 123456" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= ($cart_grand_total > 0)?'':'disabled'; ?>" value="place order">

   </form>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
