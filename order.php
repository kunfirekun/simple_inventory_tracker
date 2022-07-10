<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$name = $bet_ref = $handler = "";
$name_err = $bet_ref_err = $handler_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name.";
    } else{
        $name = $input_name;
    }
    
    // Validate bet ref
    $input_address = trim($_POST["bet_ref"]);
    if(empty($input_address)){
        $bet_ref_err = "Please enter bet reference no details.";     
    } else{
        $bet_ref = $input_address;
    }
     // Validate complaint
    $input_complaint= trim($_POST["complaint"]);
    if(empty($input_complaint)){
        $complaint_err = "Please enter the Complaint.";     
   
    } else{
        $complaint = $input_complaint;
    }
      // Validate Extras
    $input_extras = trim($_POST["extras"]);
    if(empty($input_extras)){
        $extras_err = "Please enter Extras.";     
   
    } else{
        $extras = $input_extras;
    }
    
     // Validate entry time
    $input_entry_time = trim($_POST["entry_time"]);
    if(empty($input_entry_time)){
        $entry_time_err = "Please enter the Entry Time.";     
   
    } else{
        $entry_time = $input_entry_time;
    }
    
     // Validate Resolved time
    $input_resolved_time = trim($_POST["resolved_time"]);
    if(empty($input_resolved_time)){
        $entry_resolved_time_err = "Please enter the Resolved Time.";     
   
    } else{
        $resolved_time = $input_resolved_time;
    }
    
  
    
    
    // Validate Ticket Status
    $input_ticket_status = trim($_POST["ticket_status"]);
    if(empty($input_ticket_status)){
        $ticket_status_err = "Please enter the Ticket Status.";     
   
    } else{
        $ticket_status = $input_ticket_status;
    }
    
    // Validate handler
    $input_handler = trim($_POST["handler"]);
    if(empty($input_handler)){
        $handler_err = "Please enter the Handler.";     
   
    } else{
        $handler = $input_handler;
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($bet_ref_err) && empty($handler_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO products (name, bet_ref, complaint_details, entry_time, resolved_time, ticket_status, handler) VALUES (?, ?, ? , ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssss", $param_name,$param_bet_ref, $param_complaint, $param_entry_time, $param_resolved_time, $param_ticket_status, $param_handler );
            
            // Set parameters
            $param_name = $name;
            $param_bet_ref = $bet_ref;
            $param_complaint = $complaint;
            $param_entry_time= $entry_time;
            $param_resolved_time = $resolved_time;
            $param_ticket_status = $ticket_status;
            $param_handler = $handler;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: stock.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Stock Checker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
        table tr td:last-child{
            width: 120px;
        }
    </style>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
<div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                <h1 class="my-5">Hi, Administrator <b><?php echo htmlspecialchars($_SESSION["username"]); ?>.</b></h1>
    <p >This is where you make a sale/ order , <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. </p>
                    
                  
                    <p>
<a href="order.php" class="btn btn-success">Make A Sale</a>
    <a href="sales.php" class="btn btn-info">View Sales</a>
    
    <a href="stock.php" class="btn btn-danger">Manage Stock</a>
   
    </p>
     <div class="col-md-12">
                    <div class="mt-5 mb-3 clearfix">
                        <h2 class="pull-left">Pick A Product</h2>
                       
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";
                    
                    // List numbers 1 to 20
$pages = range(100000,999999);
// Shuffle numbers
shuffle($pages);
// Get a page
$order_no = array_shift($pages);

                    // Attempt select query execution
                    $sql = "SELECT * FROM product";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<table class="table table-bordered table-striped">';
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th width='5%'>#</th>";
                                        echo "<th width='30%'>Name</th>";
                                         echo "<th width='10%'>Price</th>";
                                        echo "<th width='10%'>Description</th>";
                                   echo "<th width='10%'>Quanity</th>";
                                  echo "<th width='%'>Category</th>";
                                  
                                        echo "<th width='25%'>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . $row['name'] . "</td>";
                                        echo "<td>" . $row['bet_ref'] . "</td>";
                                        echo "<td>" . $row['complaint_details'] . "</td>";
                                         echo "<td>" . $row['extras'] . "</td>";
                                         echo "<td>" . $row['ticket_status'] . "</td>";
                                        echo "<td>";
                                       echo'<a href="sales_add.php?id='. $row['id'] .'&&orderno='.$order_no.'&&name='. $row['name'] .'&&amount='. $row['bet_ref'] .'&&desc='. $row['complaint_details'] . '&&category='. $row['ticket_status'] .'" class="btn btn-success"><span class="fa fa-shopping cart"></span>Add to Cart</a>';
                                       
                                           
                                           
                                            "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                        }
                    } else{
                        echo "Oops! Something went wrong. Please try again later.";
                    }
 
                    // Close connection
                    mysqli_close($link);
                    ?>
                </div>
                    
                </div>
            </div>        
        </div>
    </div>
   
    
    
   <br>
   <div class="wrapper">
        <div class="container-fluid">
            <div class="row" >
              <p><a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a></p>
            </div>  
             
        </div>
        
    </div>
    
</body>
</html>