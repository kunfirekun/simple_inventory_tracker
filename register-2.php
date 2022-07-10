<?php
// Include config file
require_once "config.php";

function secure_random_string($length) {
    $random_string = '';
    for($i = 0; $i < $length; $i++) {
        $number = random_int(0, 36);
        $character = base_convert($number, 10, 36);
        $random_string .= $character;
    }
 
    return $random_string;
}
 





 
 
$rand_string=secure_random_string(10);
// Define variables and initialize with empty values
$username = $email = $phone = $nid = $role= $password = $confirm_password = "";
$username_err = $email_err  = $phone_err  = $nid_err = $role_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM consumers WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } 
            else{
                echo "Oops! Something went wrong. Please try again later.";

            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
 // Prepare a select for email statement
 $sql1 = "SELECT id FROM consumers WHERE email = ?";
        
 if($stmt1 = mysqli_prepare($link, $sql1)){
     // Bind variables to the prepared statement as parameters
     mysqli_stmt_bind_param($stmt1, "s", $param_email);
     
     // Set parameters
     $param_email = trim($_POST["email"]);
     
     // Attempt to execute the prepared statement
     if(mysqli_stmt_execute($stmt1)){
         /* store result */
         mysqli_stmt_store_result($stmt1);
         
         if(mysqli_stmt_num_rows($stmt1) == 1){
             $email_err = "This email is already taken.";
         } else{
             $email = trim($_POST["email"]);
         }
     } 
     else{
         echo "Oops! Something went wrong. Please try again later.";

     }

     // Close statement
     mysqli_stmt_close($stmt1);
 }



    }

      // Validate email
      $input_email= trim($_POST["email"]);
      if(empty($input_email)){
          $email_err = "Please enter an email.";
      } elseif(!filter_var($input_email, FILTER_VALIDATE_EMAIL, array("options"=>array("regexp"=>"/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/")))){
          $email_err = "Please enter a valid email address.";
      } else{
          $email = $input_email;
      }
      
       // Validate National ID
    if(empty(trim($_POST["nid"]))){
        $nid_err = "Please enter a National Id.";     
    } elseif(strlen(trim($_POST["nid"])) < 7){
        $nid_err = "Password must have atleast 7 characters.";
    } else{
        $nid = trim($_POST["nid"]);
    }
 
    // Validate Phone
    if(empty(trim($_POST["phone"]))){
        $phone_err = "Please enter a Phone Number.";     
    } elseif(strlen(trim($_POST["phone"])) < 10){
        $phone_err = "Phone Number must have atleast 10 characters.";
    } else{
        $phone = trim($_POST["phone"]);
    }
       // Validate Role
    $input_role = trim($_POST["role"]);
    if(empty($input_role)){
        $role_err = "Please enter a Role";     
   
    } else{
        $role = $input_role;
    }
       // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 8){
        $password_err = "Password must have atleast 8 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    // Validate entry time
    $input_entry_time = trim($_POST["entry_time"]);
    if(empty($input_entry_time)){
        $entry_time_err = "Please enter the Entry Time.";     
   
    } else{
        $entry_time = $input_entry_time;
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO consumers  (username,email,tel,national_id, password,reg_date,confirm_code,role) VALUES (?, ?, ?,?,?, ?, ?,?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssss", $param_username,$param_email, $param_phone,$param_nid,$param_password, $param_entry_time, $param_rand_string,$param_role );
            
            // Set parameters
            $param_username = $username;
            $param_email=$email;
             $param_phone=$phone;
              $param_nid=$nid;
               $param_role=$role;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_entry_time= $entry_time;
            $param_rand_string= $rand_string;

  $privateKey 	= 'AA74CDCC2BBRT935136HH7B63C27'; // user define key
    $secretKey 		= '5fgf5HJ5g27'; // user define secret key
    $encryptMethod      = "AES-256-CBC";
    $string 		=$email ; // user define value

    $key = hash('sha256', $privateKey);
    $ivalue = substr(hash('sha256', $secretKey), 0, 16); // sha256 is hash_hmac_algo
    $result = openssl_encrypt($string, $encryptMethod, $key, 0, $ivalue);
    echo $output = base64_encode($result);  // output is a encripted value
    
    


            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
       
        <p>Please fill this form to create an account.</p>
        <?php echo "$ciphertext"; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>   
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>  
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>">
                <span class="invalid-feedback"><?php echo $phone_err; ?></span>
            </div> 
            <div class="form-group">
                <label>National Id</label>
                <input type="text" name="nid" class="form-control <?php echo (!empty($nid_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nid; ?>">
                <span class="invalid-feedback"><?php echo $nid_err; ?></span>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role"  class="form-control <?php echo (!empty($role_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $role; ?>">
                <option value="">Please Select Role</option>
  <option value="admin">Admin</option>
  <option value="moderator">Cashier</option>

</select>
              
                <span class="invalid-feedback"><?php echo $role_err; ?></span>
            </div>  

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <?php
                   date_default_timezone_set("Africa/Nairobi");
                   $time=date("d.m.Y, h:i:sa");
                ?>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>

            <div class="form-group">
              <?php
                    date_default_timezone_set("Africa/Nairobi");
                     $time=date("d.m.Y, h:i:sa");
                ?>
                            
             <input type="hidden" name="entry_time" class="form-control " value="<?php echo $time; ?>">
            </div>
            
         <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
           
           
           


        </form>
    </div>    
</body>
</html>