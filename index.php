<?php
  include('include/simulate.php');
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="fonts/icomoon/style.css">

    <link rel="stylesheet" href="css/owl.carousel.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <!-- Style -->
    <link rel="stylesheet" href="css/style.css">

    <title>Lipa na M-pesa</title>
    
  </head>

  <body>
  

  <div class="half">
    <div class="bg order-1 order-md-2" style="background-image: url('images/pay.png');"></div>
    <div class="contents order-2 order-md-1">
<?php   

if(isset($_SESSION["CheckoutRequestID"]) && isset($_SESSION["MerchantRequestID"])  && isset($_SESSION["phone"]))
{

   ?>


      <div class="container" id="pay">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-6">

            <div class="form-block">
              <div class="text-center mb-5">
              <h3 style="color: rgb(29, 161, 29);">Wait for <strong>Access code</strong></h3>
              <!-- <p class="mb-4">Lorem ipsum dolor sit amet elit. Sapiente sit aut eos consectetur adipisicing.</p> -->
              </div>
              <form action='#' method='POST'>
                <div class="row">
            
                  <p style="color:blue;line-height:1.7;">3.After a successful payment<b>you will,</b> receive"<b>sms with access code</b>"
                  <?php if ($errmsg != ''): ?>
                      <p style="    background: #cc2a24;padding: .8rem;color: #ffffff;"><?php echo $errmsg; ?></p>
                  <?php endif; ?>
          </div>
            
               
                
              <!--  <input type="submit" value="Confirm Payment" name="confirm_payment" id="confirm_payment" class="btn btn-block btn-primary">
                  -->
              </form>
              <a href="http://wifi.login/login"> <button  class="btn btn-success" target="_blank"> Back to login</button></a>
            </div>
          </div>
        </div>
      </div>

<?php 
session_unset();
}


else{

?>


      <div class="container" id="confirm">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-6">

            <div class="form-block">
              <div class="text-center mb-5">
              <h3 style="color: rgb(29, 161, 29);">Lipa na <strong>M-pesa</strong></h3>
              <!-- <p class="mb-4">Lorem ipsum dolor sit amet elit. Sapiente sit aut eos consectetur adipisicing.</p> -->
              </div>
              <form action='<?php echo $_SERVER['PHP_SELF'] ?>' method='POST'>
                <div class="row">
            
                  <p style="color:blue;line-height:1.7;">1. Enter the <b>phone number, select amount</b> and press "<b>Pay Now</b>"</br>2. You will receive a popup on your phone. Enter your <b>MPESA PIN</b></p>
                  <?php if ($errmsg != ''): ?>
                      <p style="    background: #cc2a24;padding: .8rem;color: #ffffff;"><?php echo $errmsg; ?></p>
                  <?php endif; ?>
          </div>
                <div class="form-group first">
             
                  <label for="username">Phone Number</label>
                  <input type="text" class="form-control" name="phone_number" placeholder="0716815356" id="username" maximum="10" required>

                 
                </div>

                
                <div class="form-group first mb-2" style="background-color: transparent; border: none;  padding: 0 1em 0 0;  margin: 0;   width: 100%;   font-family: inherit; font-size: inherit;  cursor: inherit;">
                  <label for="password"><span style="color:black; font-weight:bold;">Select Amount: </span></label>
                  <select name="amount" id="amount" required>
              
                    <option value="1" selected>KSH 25 for 6 hours</option>
                    <option value="35">KSH 35 for 12 hours</option>
                    <option value="35">KSH 45 for 24 hours</option>
                    <option value="">KSH 150 for 7days</option>
                    <option value="500">KSH 500 for 30days</option>
                  </select>
               
             
                </div>
                
                <input type="submit" value="Pay now" name="pay" class="btn btn-block btn-primary">

              </form>
            </div>
          </div>
        </div>
      </div>

<?php
}
?>





    
    </div>

  </div>
    
    

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>