<?php

/**
 * This file holds things for processing input, checks validations, and finally saves the whole info
*/

require_once("config.php");
require_once("validation.php");

if((isset($_POST['your_name'])&& $_POST['your_name'] !='') && (isset($_POST['your_email'])&& $_POST['your_email'] !=''))
{
    $name = clean_input($conn->real_escape_string($_POST['your_name']));
    $email = clean_input($conn->real_escape_string($_POST['your_email']));
    $phone = clean_input($conn->real_escape_string($_POST['your_phone']));
    $date = date("Y-m-d h:i:s");
    // Validation
    if(!valid_email($email)) die("! Invalid email address, try again...");
    if(!valid_phone($phone)) die("! Invalid phone number, try again...");

    if(empty($_GET['go'])) $sql="INSERT INTO `journal` (`name`, `email`, `phone`, `time`) VALUES ('".$name."','".$email."', '".$phone."', '".$date."')";

    else if($_GET['go']=='update' && empty($_GET['id'])) die("Unable to find the visitor ID to update...");
    else if ($_GET['go'] == 'update' && !empty($_GET['id'])) {
        $id = $_GET['id'];
        $sql="UPDATE `journal` SET `name` = '".$name."', `email` = '".$email."', `phone` = '".$phone."' WHERE id=$id";
    }
    if(!$result = $conn->query($sql)){
        die('! Error performing database operation: ' . $conn->error . ']');
    }
    else {
        echo "Operation completed successfully!";
    }
}
else
echo "Something\'s wrong, sorry";
?>