<?php 

session_start();
include('sm.php');
require_once '../include/conn.php';



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lipa na Mpesa</title>
    <link href="https://fonts.googleapis.com/css2?family=Muli:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style-with-prefix.css">
    <style>
        .srouce{
            text-align: center;
            color: #ffffff;
            padding: 10px;
        }
    </style>
</head>
<body>



<?php

if(!isset($_SESSION["CheckoutRequestID"]) && !isset($_SESSION["MerchantRequestID"])  && !isset($_SESSION["orderNo"]))

{
?>

    <div class="main-container">
        <div class="form-container">

            <div class="srouce"><a title="Learn How to create Beautiful HTML & CSS login page template" href="#">Cloud Galaxy</a></div>

            <div class="form-body">
                <h2 class="title" style="color:green;">Lipa Na Mpesa</h2>
                <div class="social-login">
                    <ul>
                        <li class=""><a href="#">Mpesa Online</a></li>
                        <li class=""><a href="till.php">Switch to till</a></li>
                    </ul>
                </div><!-- SOCIAL LOGIN -->

                <div class="_or">or</div>
                <div class="row">
            
            <p style="color:yellow;line-height:1.7;">1. Enter the <b>phone number, and amount</b> then press "<b>Pay Now</b>"</br>2. You will receive a popup on your phone. Enter your <b>MPESA PIN</b></p>
            <?php if ($errmsg != ''): ?>
                <p style="background: #cc2a24;padding: .8rem;color: #ffffff;"><?php echo $errmsg; ?></p>
            <?php endif; ?>
    </div>
                <form class="the-form" action='<?php echo $_SERVER['PHP_SELF']?>' method='POST'>


                    <label for="email">Phone Number(start with '0')</label>
                    <input type="number" name="phone_number"  id="email" placeholder="0712345678" required autocomplete="on">

                    <label for="password">Amount</label>
                    <input type="number" name="amount" min="1" id="password" placeholder="35" required autocomplete="off">

                    <input type="submit" value="Pay Now" name="pay">

                </form>

            </div><!-- FORM BODY-->


            

            <div class="form-footer">
                <div>
                    <span>Didn't receive Mpesa Popup?</span> <a href="till.php">Switch to till</a>
                </div>
            </div><!-- FORM FOOTER -->

        </div><!-- FORM CONTAINER -->
    </div>

<?php
}
else{
   // header("location:confirmpay.php");
  // session_unset();
}
?>


</body>
</html>