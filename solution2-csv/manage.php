<?php

/**
 * This file holds things for processing input, checks validations, and finally saves the whole info
*/

require_once("validation.php");
require_once("csv.php");

if((isset($_POST['your_name'])&& $_POST['your_name'] !='') && (isset($_POST['your_email'])&& $_POST['your_email'] !=''))
{
    $name = clean_input($_POST['your_name']);
    $email = clean_input($_POST['your_email']);
    $phone = clean_input($_POST['your_phone']);
    $date = date("Y-m-d h:i:s");
    // Validation
    if(!valid_email($email)) die("! Invalid email address, try again...");
    if(!valid_phone($phone)) die("! Invalid phone number, try again...");

    if(empty($_GET['go'])) {
        $content = getDataFromCSV('visitors.csv');
        $index = count($content);
        if($index==0) $id=1;
        else $id = $content[$index-1][0]+1; // +1 to the last record to make ID bigger
        $data = array(array($id, $name, $email, $phone, $date));
        writeDataToCSV('visitors.csv','a',$data);
    }

    else if($_GET['go']=='update' && empty($_GET['id'])) die("Unable to find the visitor ID to update...");
    else if ($_GET['go'] == 'update' && !empty($_GET['id'])) {
        $id = $_GET['id'];
        $data = getDataFromCSV('visitors.csv');
        $findId = array_search($id, array_column($data, 0));
        $data[$findId][1] = $name;
        $data[$findId][2] = $email;
        $data[$findId][3] = $phone;
        writeDataToCSV('visitors.csv','w',$data);
    }
    echo "Operation completed successfully!";
}
else
echo "Something\'s wrong, sorry";
?>