<?php

// include the class file and create a new object.
require_once('../FormHelper.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML Form Example - Comparison</title>
    <link rel="stylesheet" href="./style.css?x=2">
</head>

<body>
    <h1>HTML Form Example - Comparison</h1>
    <div><a href="./">&laquo; back to All Examples</a></div><br><br>

    <h2>Display form using the class</h2>
    <?php 

    // initialzie class
    $form = new FormHelper();
    // automatically add the id field
    $form->setDoAddIdAttributeFromName(true);
    // for dropdown (select) inputs, the value is the same as the text displayed
    $form->setDoSelectOptionValueEqualsText(true);

    // get passed values. set to empty string ("") if not set. strip tags and trim
    $full_name1 = $form->getPassed('full_name1');
    $color1 = $form->getPassed('color1');
    $comments1 = $form->getPassed('comments1');
    $form_load_time1 = $form->getPassed('form_load_time1');

    if (!empty($form_load_time1)) {
        // form can be processed here
        echo "<b>Form submitted</b>";
        echo "<pre>";
        var_dump($_POST); 
        echo "</pre><br>";
    }

    
    
    // form start tag
    $form->formStart();

    $form->hidden('form_load_time1', date('h:i:sA'));
    
    ?>
    <div>Full Name</div>
    <?php $form->text('full_name1', $full_name1); ?><br><br>

    <div>Favorite Color</div>
    <?php 
        $colors = array('', 'red', 'green', 'blue', 'red & blue');
        $form->select('color1', $colors, $color1);
    ?><br><br>

    <div>Comments</div>
    <?php $form->textarea('comments1', $comments1); ?><br><br>

    <?php $form->button('Save Info') ?>

    <?php $form->formEnd(); ?>


    <br><br><br>



    <h2>Display form WITHOUT using the class</h2>
    <?php 

    // get passed values. set to empty string ("") if not set. strip tags and trim
    $full_name2 = $color2 = $comments2 = $form_load_time2 = "";
    if (isset($_POST['full_name2'])) {
        $full_name2 = $_POST['full_name2'];
        $full_name2 = strip_tags($full_name2);
        $full_name2 = trim($full_name2);
    }
    if (isset($_POST['color2'])) {
        $color2 = $_POST['color2'];
        $color2 = strip_tags($color2);
        $color2 = trim($color2);
    }
    if (isset($_POST['comments2'])) {
        $comments2 = $_POST['comments2'];
        $comments2 = strip_tags($comments2);
        $comments2 = trim($comments2);
    }
    if (isset($_POST['form_load_time2'])) {
        $form_load_time2 = $_POST['form_load_time2'];
        $form_load_time2 = strip_tags($form_load_time2);
        $form_load_time2 = trim($form_load_time2);
    }

    if (!empty($form_load_time2)) {
        // form can be processed here
        echo "<b>Form submitted</b>";
        echo "<pre>";
        var_dump($_POST); 
        echo "</pre><br>";
    }
?>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">

        <input type="hidden" name="form_load_time2" id="form_load_time2" value="<?php echo date('h:i:sA'); ?>">

        <div>Full Name</div>
        <input type="text" name="full_name2" id="full_name2" value="<?php echo htmlentities($full_name2); ?>"><br><br>

        <div>Favorite Color</div>
        <select name="color2" id="color2">
            <option value="" <?php if ($color2 === "") { echo "selected"; } ?>></option>
            <option value="red" <?php if ($color2 === "red") { echo "selected"; } ?>>red</option>
            <option value="green" <?php if ($color2 === "green") { echo "selected"; } ?>>green</option>
            <option value="blue" <?php if ($color2 === "blue") { echo "selected"; } ?>>blue</option>
            <option value="red &amp; blue" <?php if ($color2 === "red & blue") { echo "selected"; } ?>>red &amp; blue</option>
        </select><br><br>

        <div>Comments</div>
        <textarea name="comments2" id="comments2"><?php 
            echo htmlentities($comments2); 
        ?></textarea><br><br>

        <button>Save Info</button>

    </form>


</body>

</html>