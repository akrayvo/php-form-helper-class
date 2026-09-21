<?php

// include the class file
require_once('../FormHelper.class.static.php');
// initialize class

// get the value passed to the page. check both $_POST and $_GET
$name = FormHelper::getPassed('name');
$sport = FormHelper::getPassed('sport');
$comments = FormHelper::getPassed('comments');

// hard-coded options for select (dropdown menu) field
// in actual usage, this data could also come from a database or data file
$sports = array(
    '' => '- select a sport -',
    'baseball' => 'Baseball & Softball',
    'basketball' => 'basketball',
    'football' => 'Football'
);

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML Form Example - Basic</title>
    <link rel="stylesheet" href="./style.css?x=2">
</head>

<body>
    <h1>HTML Form Example - Basic</h1>
    <div><a href="./">&laquo; back to All Examples</a></div><br><br>

    <p>All functions and settings in the static version of the class are the same as the normal version.</p>

    <?php
    if (!empty($_POST)) {
        // form can be processed here
        echo "<div><b>Form submitted</b>";
        echo "<pre>";
        var_dump($_POST); 
        echo "</pre></div><br>";
    } 
    ?>

    <?php FormHelper::formStart(); ?>

    <?php FormHelper::hidden('date_loaded', date('h:i:sA')); ?>

    <div><label>Name</label></div>
    <?php FormHelper::text('name', $name); ?><br><br>

    <div><label>Favorite Sport</label></div>
    <?php FormHelper::select('sport', $sports, $sport); ?><br><br>

    <div><label>Comments</label></div>
    <?php FormHelper::textarea('comments', $comments); ?><br><br>

    <?php FormHelper::button('Save Info'); ?>

    <?php FormHelper::formEnd(); ?>

</body>

</html>