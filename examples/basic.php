<?php

// include the class file
require_once('../FormHelper.class.php');
// initialize class
$form = new FormHelper();

// get the value passed to the page. check both $_POST and $_GET
$name = $form->getPassed('name');
$color = $form->getPassed('color');
$comments = $form->getPassed('comments');

// hard-coded options for select (dropdown menu) field
// in actual usage, this data could also come from a database or data file
$colors = array(
    '' => '- select a color -',
    'blue' => 'Blue',
    'green' => 'Green',
    'lightBlue' => 'Light Blue',
    'red' => 'Red'
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

    <?php $form->formStart(); ?>

    <div><label>Name</label></div>
    <?php $form->text('name', $name); ?><br><br>

    <div><label>Favorite Color</label></div>
    <?php $form->select('color', $colors, $color); ?><br><br>

    <div><label>Comments</label></div>
    <?php $form->textarea('comments', $comments); ?><br><br>

    <?php $form->submit('Save Info'); ?>

    <?php $form->formEnd(); ?>

</body>

</html>