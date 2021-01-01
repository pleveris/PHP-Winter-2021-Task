<?php

/**
 * index.php - the main project file

*/

include("config.php");

/**
 * Gets info about all available restaurant visitors
 *
 * @param $c - MySql connection
 * @return array data
 * Critical error if failed to take info from database
 */

function getAllVisitors($c) {
    $data = $c->query("SELECT * FROM `journal` ORDER BY id");
    if(!$data) {
        die('There was an error running the query [' . $c->error . ']');
    }
    else return $data;
}

/**
 * Gets info about chosen restaurant visitor
 *
 * @param $c - MySql connection
 * @param $id - ID of the visitor
 * @return array data
 * Critical error if failed to take info from database
 */

function getVisitorInfoById($c, $id) {
    $data = $c->query("SELECT * FROM `journal` WHERE id=$id");
    if(!$data) {
        die('There was an error running the query [' . $c->error . ']');
    }
    else {
        return $data->fetch_assoc();
    }
}

/**
 * Adds info about visitor to a database
 *
 * @param $c - MySql connection
 * @param $id - ID of the visitor
 * @param $name - visitor's name
 * @param $email - visitor's email address
 * @param $phone - visitor's phone number
 * @param $date - visitor's registration date
 * @return bool
 * Critical error if failed to take info from database
 */

function addVisitorToSql ($c, $id, $name, $email, $phone, $date) {
    $data = $c->query("INSERT INTO `journal` (`id`, `name`, `email`, `phone`, `time`) VALUES ('".$id."', '".$name."', '".$email."', '".$phone."', '".$date."')");
    if(!$data) die('! Error performing database operation: ' . $c->error . ']');
    return true;
}

/**
 * Gets info about visitors from CSV data file
 *
 * @param $csv_file - CSV data file name
 * @return array csv_result
 */

function getDataFromCSV($csv_file) {
    $csv_result = [];
    if(!file_exists($csv_file)) return $csv_result;
    if (($file = fopen($csv_file, "r")) !== false) {
        while (($data = fgetcsv($file)) !== FALSE) {
            $csv_result[] = $data;
        }
        fclose($file);
    }
    return $csv_result;
}

/**
 * Starts the webpage
 */

function webpage_start() {
    echo '
<!DOCTYPE html>
<html>
<head> 
<title>Visitor Management System for Restaurants</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<style>

.navigation {
background-color: #333;
overflow: hidden;
}

.data, .data * {background-color: #001F07!important}

table {color: #FFFFEF;}
table.text1 {background-color: #001F00;
border-color: #AFAFE0;
border-spacing: 0px;
border-width: 2px
}

td.soft { border-width: thin; border-color: #7F7F7F; }
td.text2 {font-size: 125%; text-indent:5px; }

th {color: #FFFFBF; font-size: 133%; }

</style>
</head>
<body>
<div class="container">
<div class="row">
<div class="col-md-8">
<h1>Visitor Management System for Restaurants</h1>
<div class="navigation">
<ul>
<a href="?go=add">Add a new visitor</a>
<a href="?go=show">Show all visitors</a>
    <a href="?go=import">Import data from CSV</a>
</ul>
</div>
<br/>
';
}

/**
 * Ends the webpage content
 * All the JavaScript handeling (ajax, validation)
 */

function webpage_end () {

    echo '
</div>
</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $("#contact-form").on("submit",function(e){
            e.preventDefault();
            if($("#contact-form [name=\'your_name\']").val() === \'\')
            {
                $("#contact-form [name=\'your_name\']").css("border","1px solid red");
            }
            else if ($("#contact-form [name=\'your_email\']").val() === \'\')
            {
                $("#contact-form [name=\'your_email\']").css("border","1px solid red");
            }
            else
            {
                var sendData = $( this ).serialize();
                if($("#contact-form [name=\'update_id\']").val() >0)
                {
                    $.ajax({
                        type: "POST",
                        url: "manage.php?go=update&id="+$("#contact-form [name=\'update_id\']").val(),
                        data: sendData,
                        success: function(data){
                            alert(data);
                            if(data.charAt(0) != "!") $("#contact-form").find("input[type=text], input[type=email]").val("");
                        }
                    });
                }
                else {
                    $.ajax({
                        type: "POST",
                        url: "manage.php",
                        data: sendData,
                        success: function(data){
                            alert(data);
                            if(data.charAt(0) != "!") $("#contact-form").find("input[type=text], input[type=email]").val("");
                        }
                    });
                }
            }
        });
    $("#contact-form input").blur(function(){
        var checkValue = $(this).val();
        if(checkValue != \'\')
        {
            $(this).css("border","1px solid #eeeeee");
        }
    });
});
</script>
</body>
</html>

    ';
}

/**
 * Show input form according to given criterias
 * @param $data - array of content to fill the form automatically (if editing visitor info)
 * @param $param - indicator what form to call in the script (when creating a new visitor, or ediiting details of created one)

 */

function showForm ($data, $param) {
    echo '
<form name="contact-form" action="" method="post" id="contact-form">
<div class="form-group">
<label for="Name">Name</label>
<input type="text" class="form-control" name="your_name" ';
    if($param==2) echo 'value="'.$data['name'].'" ';
    echo 'placeholder="Name" required>
</div>
<div class="form-group">
<label for="Email">Email address</label>
<input type="email" class="form-control" name="your_email" ';
    if($param==2) echo 'value="'.$data['email'].'" ';
    echo 'placeholder="Email" required>
</div>
<div class="form-group">
<label for="Phone">Phone number</label>
<input type="text" class="form-control" name="your_phone" ';
    if($param==2) echo 'value="'.$data['phone'].'" ';
    echo 'placeholder="Phone" required>
</div>';
    if($param==2) echo '<input type="hidden" class="form-control" name="update_id" value="'.$data['id'].'"/>';
    echo '<button type="submit" class="btn btn-primary" name="submit" value="Submit" id="submit_form">Submit</button>

</form>
    ';
}

webpage_start();

if(empty($_GET['go']) || $_GET['go'] == 'show') {
    $data = getAllVisitors($conn);
    echo '<h2>Visitors: </h2><br/>';
    if($data->num_rows==0) echo 'There are no visitors at the moment. You can <a href="?go=add">add a new visitor</a> or <a href="?go=import">import visitor data from CSV file</a><br/>';
    else {
        echo '
<div class="data">

 <table class="text1" cellpadding="5" border="1">
	<tbody>
<tr valign="middle">
<th> ID </th>
<th> Name </th>
<th> Email </th>
<th> Phone number </th>
<th> Registration time </th>
<th> More actions </th>
</tr>
        ';

        while($row = $data->fetch_assoc()) {
            echo '
<tr>
<td class="text2">'.$row['id'].'</td>
<td class="text2">'.$row['name'].'</td>
<td class="text2"><a href="mailto:'.$row['email'].'">'.$row['email'].'</a></td>
<td class="text2">'.$row['phone'].'</td>
<td class="text2">'.$row['time'].'</td>
<td class="text2">
<ul>
<li><a href="?go=edit&id='.$row['id'].'">Edit info</a></li>
<li><a href="?go=delete&id='.$row['id'].'">Delete</a></li>
</ul>
</td>
</tr	>
            ';
        }
        echo '
</tbody>
</table>
</div>';
    }
}

else if ($_GET['go'] == 'add') showForm([], 1);
else if($_GET['go'] == 'edit') {
    $id = $_GET['id'];
    if (empty($id)) echo "The ID is invalid...";
    else {
        $data = getVisitorInfoById($conn, $id);
        showForm($data, 2);
    }
}
else if($_GET['go'] == 'delete') {
    $id = $_GET['id'];
    if (empty($id)) echo "The ID is invalid...";
    else {
        echo '<h2>Are you sure?</h2><br/>
Are you sure you want to delete this visitor?<br/>
<a href="?go=confirmdel&id='.$id.'">Delete</a><br/>
<a href="index.php">Cancel</a><br/>';
    }
}

else if($_GET['go'] == 'confirmdel') {
    $id = $_GET['id'];
    if (empty($id)) echo "The ID is invalid...";
    else {
        $sql="DELETE FROM `journal` WHERE id=$id";
        if(!$result = $conn->query($sql)){
            die('! Error performing database operation: ' . $conn->error . ']');
        }
        else
        {
            echo "Operation completed successfully!";
        }
    }
}

else if($_GET['go'] =='import') {
    echo '<h2>Following data received from CSV file: </h2><br/>';
//$file = fopen("data.csv","r");
//print_r(fgetcsv($file));
//fclose($file);

//print_r($csv_result);
    $data = getDataFromCsv("data.csv");
    echo '<ul>';
    for($i=0; $i<count($data); $i++) {
        echo '<li>'.$data[$i][0].'. <br/>
Name: '.$data[$i][1].',<br/>
Email: <a href="mailto:'.$data[$i][2].'">'.$data[$i][2].'</a>,<br/>
Phone number: '.$data[$i][3].',<br/>
Registration time: '.$data[$i][4].';</li>';
    }
    echo '</ul><br/>';
    echo '
<a href="?go=confirmimp">Import</a><br/>
<a href="index.php">Cancel</a><br/>';
}
else if($_GET['go'] == 'confirmimp') {
    $data = getDataFromCsv("data.csv");
    if(!$data) echo 'Error getting data from CSV...';
    else
    for($i=0; $i<count($data); $i++)
    $res = addVisitorToSql($conn, $data[$i][0], $data[$i][1], $data[$i][2], $data[$i][3], $data[$i][4]);
    if($res) echo 'Import successful!<br/>';
}

webpage_end();

?>