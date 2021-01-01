<?php

/**
 * index.php - the main project file

*/

require_once("csv.php"); // CSV processing

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
    if($param==2) echo 'value="'.$data[1].'" ';
    echo 'placeholder="Name" required>
</div>
<div class="form-group">
<label for="Email">Email address</label>
<input type="email" class="form-control" name="your_email" ';
    if($param==2) echo 'value="'.$data[2].'" ';
    echo 'placeholder="Email" required>
</div>
<div class="form-group">
<label for="Phone">Phone number</label>
<input type="text" class="form-control" name="your_phone" ';
    if($param==2) echo 'value="'.$data[3].'" ';
    echo 'placeholder="Phone" required>
</div>';
    if($param==2) echo '<input type="hidden" class="form-control" name="update_id" value="'.$data[0].'"/>';
    echo '<button type="submit" class="btn btn-primary" name="submit" value="Submit" id="submit_form">Submit</button>

</form>
    ';
}

webpage_start();

if(empty($_GET['go']) || $_GET['go'] == 'show') {
    $data = getDataFromCSV('visitors.csv');
    echo '<h2>Visitors: </h2><br/>';

    if(count($data)==0) echo 'There are no visitors at the moment. You can <a href="?go=add">add a new visitor</a> or <a href="?go=import">import visitor data from CSV file</a><br/>';
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
        for($i=0; $i<count($data); $i++) {
            echo '
<tr>
<td class="text2">'.$data[$i][0].'</td>
<td class="text2">'.$data[$i][1].'</td>
<td class="text2"><a href="mailto:'.$data[$i][2].'">'.$data[$i][2].'</a></td>
<td class="text2">'.$data[$i][3].'</td>
<td class="text2">'.$data[$i][4].'</td>
<td class="text2">
<ul>
<li><a href="?go=edit&id='.$data[$i][0].'">Edit info</a></li>
<li><a href="?go=delete&id='.$data[$i][0].'">Delete</a></li>
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
$data = getDataFromCSV('visitors.csv');
$findId = array_search($id, array_column($data, 0));
$dat = array($data[$findId][0], $data[$findId][1], $data[$findId][2], $data[$findId][3], $data[$findId][4]);
//        $data = getVisitorInfoById($conn, $id);
        showForm($dat, 2);
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
        $data = getDataFromCSV('visitors.csv');
        $findId = array_search($id, array_column($data, 0));
        unset($data[$findId]);
        writeDataToCSV('visitors.csv','w',$data);
        echo "Operation completed successfully!";
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
    writeDataToCSV('visitors.csv', 'a', $data);
    echo 'Import successful!<br/>';
}

webpage_end();

?>