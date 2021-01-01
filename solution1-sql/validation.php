<?php

/**
 * This file holds form validation functions
 */

function valid_email($email) {
    $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
    if (preg_match($regex, $email) && filter_var($email, FILTER_VALIDATE_EMAIL)) return true;
    else return false;
}

function valid_phone($number) {
    if(preg_match('/^\+[0-9]{3} [0-9]{3} [0-9]{2} [0-9]{3}$/', $number) || preg_match('/^\+[0-9]{3}[0-9]{3}[0-9]{2}[0-9]{3}$/', $number)) return true;
    else return false;
}

function clean_input($data) {
$data = trim($data);
$data = stripslashes($data);
$data = htmlspecialchars($data);
return $data;
}

?>