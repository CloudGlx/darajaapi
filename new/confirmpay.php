<?php 

session_start();

require_once '../include/conn.php';
//SAVE SESSEION TO VALIABLE
$orderSession=$_SESSION['orderNo'];
if(isset($_POST['confirm'])){
    //wait for 3 secods
   // sleep(3);

//QJO9ILI6ZV

$sql="SELECT * FROM requst WHERE Orderid='$orderSession'";
$re=mysqli_query($conn,$sql);
$row= mysqli_fetch_row($re);

$check=$row[8];
$status=$row[10];
if($status=='error'){
$_SESSION['error']=$status;
unset($_SESSION['orderNo']);

}

}
//session_destroy();

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
if(isset($_SESSION['orderNo'])){
    if(empty($check)){
?>
<div class="main-container" id='till'>
        <div class="form-container">

            <div class="srouce"><a title="Learn How to create Beautiful HTML & CSS login page template" href="#">Cloud Galaxy</a></div>

            <div class="form-body">
                <h2 class="title" style="color:green">Confirm Payments</h2>
                <div class="social-login">
                  
                </div><!-- SOCIAL LOGIN -->

                <div class="row">
            
            <p style="color:yellow;line-height:1.7;">1.After recieving the payment confirmation message press,"<b>Confirm Payments  </b>"to receive your code</br>
            <?php if ($errmsg != ''): ?>
                <p style="background: #cc2a24;padding: .8rem;color: #ffffff;"><?php echo $errmsg; ?></p>
            <?php endif; ?>
    </div>
            <form method="post">
    <button  class="btn btn-success" type="submit" name="confirm" target="_blank" style="background-color:green;border: none;text-align: center;  text-decoration: none;  display: inline-block;padding: 15px 20px;">CONFIRM PAYMENTS</button>
            </div><!-- FORM BODY-->
            </form> 
         <div class="form-footer">
                <div>
             
                </div>
            </div><!-- FORM FOOTER -->

        </div><!-- FORM CONTAINER -->
    </div>

<?php
 }elseif(!empty($check)){
    
 ?>
    

    <div class="main-container" id='till'>
        <div class="form-container">

            <div class="srouce"><a title="Learn How to create Beautiful HTML & CSS login page template" href="#">Cloud Galaxy</a></div>

            <div class="form-body">
                <h2 class="title" style="color:green;font-weight:bold;">Payments Received</h2>
                <div class="social-login">
          
                </div><!-- SOCIAL LOGIN -->

                <div class="row">
            
            <p style="color:yellow;line-height:1.7;">1.Please wait for Access code sms "<b>in a few</b>"</br>
            <?php if ($errmsg != ''): ?>
                <p style="background: #cc2a24;padding: .8rem;color: #ffffff;"><?php echo $errmsg; ?></p>
            <?php endif; ?>
    </div>
             
    <a href="http://wifi.login/login"> <button  class="btn btn-success" target="_blank" style="background-color:green;border: none;text-align: center;  text-decoration: none;  display: inline-block;padding: 15px 20px;">Back to Login</button></a>
            </div><!-- FORM BODY-->

         <div class="form-footer">
                <div>
                    <span>Didn't receive Access code?</span> <a href="tel:+254716815356">Click here</a>
                </div>
            </div><!-- FORM FOOTER -->

        </div><!-- FORM CONTAINER -->
    </div>
<?php
session_unset();
 }
 else{
 ?>


<div class="main-container" id='till'>
        <div class="form-container">

            <div class="srouce"><a title="Learn How to create Beautiful HTML & CSS login page template" href="#">Cloud Galaxy</a></div>

            <div class="form-body">
                <h2 class="title" style="color:red">Error Occurred</h2>
                <div class="social-login">
                  
                </div><!-- SOCIAL LOGIN -->

                <div class="row">
            
            <p style="color:yellow;line-height:1.7;">1.Sorry, We cannot verify your payments at this time "<b>Make sure you have received Mpesa confirmation sms</b>"</br>
            <?php if ($errmsg != ''): ?>
                <p style="background: #cc2a24;padding: .8rem;color: #ffffff;"><?php echo $errmsg; ?></p>
            <?php endif; ?>
    </div>
             
    <a href="http://wifi.login/login"> <button  class="btn btn-success" target="_blank" style="background-color:green;border: none;text-align: center;  text-decoration: none;  display: inline-block;padding: 15px 20px;">VERIFY AGAIN</button></a>
            </div><!-- FORM BODY-->

         <div class="form-footer">
                <div>
                    <span>Didn't verify Payments?</span> <a href="tel:+254716815356">Click here</a>
                </div>
            </div><!-- FORM FOOTER -->

        </div><!-- FORM CONTAINER -->
    </div>
   
<?php
 }
 ?>


<?php
}elseif(isset($_SESSION['error'])){
 ?>   

<div class="main-container" id='till'>
        <div class="form-container">

            <div class="srouce"><a title="Learn How to create Beautiful HTML & CSS login page template" href="#">Cloud Galaxy</a></div>

            <div class="form-body">
                <h2 class="title" style="color:red">Error Occurred</h2>
                <div class="social-login">
                  
                </div><!-- SOCIAL LOGIN -->

                <div class="row">
            
            <p style="color:yellow;line-height:1.7;">1.Sorry, We cannot verify your payments at this time "<b>Make sure you have received Mpesa confirmation sms</b>"</br>
            <?php if ($errmsg != ''): ?>
                <p style="background: #cc2a24;padding: .8rem;color: #ffffff;"><?php echo $errmsg; ?></p>
            <?php endif; ?>
    </div>
             
    <a href="#"> <button  class="btn btn-success" target="_blank" style="background-color:green;border: none;text-align: center;  text-decoration: none;  display: inline-block;padding: 15px 20px;">VERIFY AGAIN</button></a>
            </div><!-- FORM BODY-->

         <div class="form-footer">
                <div>
                    <span>Didn't verify Payments?</span> <a href="tel:+254716815356">Click here</a>
                </div>
            </div><!-- FORM FOOTER -->

        </div><!-- FORM CONTAINER -->
    </div>

<?php
 session_destroy();
}else{
   
    header('Location:index.php');
}

?>

</body>
</html>