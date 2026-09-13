<?php

// include the class file
require_once('../FormHelper.php');

$form = new FormHelper();

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML Form Example - Settings</title>
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <h1>HTML Form Example - Basic</h1>
    <div><a href="./">&laquo; back to All Examples</a></div><br><br>

    <?php 
    $form->formStart();
    ?>



    <!--
    setDoAddIdAttributeFromName()
    -->

    <h2>setDoAddIdAttributeFromName()</h2>
    <ul>
        <li>automatically add an <b>id` attribute with the same value as the name attribute</li>
        <li>does not affect radio inputs because they can have multiple elements with the same name attribute</li>
        <li>does not affect buttons because they don't generally have name attributes</li>
        <li>true or false (boolean); default = <b>false</b></li>
    </ul>

    <div class="section-label">doAddIdAttributeFromName is set to <b>false</b>. id is not passed. no id attribute will
        be on the tag.</div>
    <div><?php
        $form->setDoAddIdAttributeFromName(false);
        $form->text("full_name_3", "", array("placeholder"=>"Full Name 3"));
        // output: <input type="text" name="full_name_3" value="" placeholder="Full Name 3">
    ?></div>

    <div class="text-label">doAddIdAttributeFromName is set to <b>true</b>. The id is automatically set to the same
        value as name (parameter 1).</div>
    <div><?php 
        $form->setDoAddIdAttributeFromName(true);
        $form->text("full_name_1", "", array("placeholder"=>"Full Name 1"));
        // output: <input type="text" name="full_name_1" value="" placeholder="Full Name 1" id="full_name_1">
    ?></div>

    <div class="section-label">doAddIdAttributeFromName is still to <b>true</b>. But an id is passed through the
        $moreAttributes parameter, overwriting the automatic value.</div>
    <div><?php 
        $form->text("full_name_2", "", array("placeholder"=>"Full Name 2", "id"=>"name2"));
        // output: <input type="text" name="full_name_2" value="" placeholder="Full Name 2" id="name2">
    ?></div>




    <br><br><br>


    <!--
    setDoReturnHtml()
    -->

    <h2>setDoReturnHtml()</h2>
    <ul>
        <li>return the HTML elements as a string</li>
        <li>true or false (boolean); default = <b>false</b></li>
    </ul>
    <div class="section-label">doReturnHtml is set to <b>true</b>. The HTML input field is returned rather than
        output. The PHP <b>echo</b> is required for output.</div>
    <div><?php 
        $form->setDoReturnHtml(true);
        $fieldHtml = $form->text("full_name_1", "", array("placeholder"=>"Full Name 1"));
        echo $fieldHtml;
        // output: <input type="text" name="full_name_1" value="" placeholder="Full Name 1">
    ?></div>

    <div class="section-label">doReturnHtml is set to <b>false</b>. The HTML input is directly output rather than returned. 
        No echo command is necessary.
    </div>
    <div><?php
        $form->setDoReturnHtml(false);
        $form->text("full_name_2", "", array("placeholder"=>"Full Name 2"));
        // output: <input type="text" name="full_name_2" value="" placeholder="Full Name 2">
    ?></div>


    <br><br><br>


    <!--
    setIsXhtmlStyle()
    -->

    <h2>setIsXhtmlStyle()</h2>
    <ul>
        <li>output XHTML-style HTML</li>
        <li>use a closing slash on HTML tags that do not require a closing tag, ex: 
            <?php echo htmlspecialchars('<input type="text" name="name">'); ?> vs
            <?php echo htmlspecialchars('<input type="text" name="name" />'); ?></li>
        <li>add an attribute name for attributes that do not require them, ex: 
            <?php echo htmlspecialchars('<button disabled>Submit</button>'); ?> vs
            <?php echo htmlspecialchars('<button disabled="disabled">Submit</button>'); ?></li>
        <li>note that this should generally be false, but can be set to true for consistency with existing code</li>    
    </ul>
    <div class="section-label">setIsXhtmlStyle is set to <b>true</b>. There is a forward slash at the end of the input tag
        and readonly has a value of "readonly".</div>
    <div><?php 
        $form->setIsXhtmlStyle(true);
        $form->text("full_name_1", "", array("placeholder"=>"Full Name 1", "readonly"));
        // output: <input type="text" name="full_name_1" value="" placeholder="Full Name 1" readonly="readonly" />
    ?></div>

    <div class="section-label">setIsXhtmlStyle is set to <b>false</b>. There is no forward slash at the input tag and
        readonly has no value.</div>
    <div><?php
        $form->setIsXhtmlStyle(false);
        $fieldHtml = $form->text("full_name_2", "", array("placeholder"=>"Full Name 2", "readonly"));
        // output: <input type="text" name="full_name_2" value="" placeholder="Full Name 2" readonly>
    ?></div>



    <br><br><br>


    <!--
    setDoPassedStringCleanup()
    -->

    <h2>setDoPassedStringCleanup()</h2>
    <ul>
        <li>string cleanup of passed variables - removes HTML tags and strips beginning and end whitespace</li>
        <li>true or false (boolean); default = <b>true</b></li>
    </ul>

    <div><b>* submit the form with beginning/end whitespace and code to preview/test.</b></div>

    <div class="section-label">setDoPassedStringCleanup is set to <b>true</b>. Beginning whitespace, ending whitespace,
        and HTML tags will be removed.</div>
    <div><?php
    $form->setDoPassedStringCleanup(true);
    $full_name_cleanup = $form->getPassed("full_name_cleanup");
    $form->text("full_name_cleanup", $full_name_cleanup, array("placeholder"=>"Full Name Cleanup"));
    
    // if input = " <b>Joe</b> <li>Smith</i> ", then output: <input type="text" name="full_name_cleanup" value="Joe Smith" placeholder="Full Name Cleanup">
    echo "<div>value=\"".htmlspecialchars($full_name_cleanup)."\"</div>";
    ?></div>

    <div class="section-label">setDoPassedStringCleanup is set to <b>false</b>. Beginning whitespace, ending whitespace,
        and HTML tags will remain.</div>
    <div><?php
    $form->setDoPassedStringCleanup(false);
    $full_name_no_cleanup = $form->getPassed("full_name_no_cleanup");
    $form->text("full_name_no_cleanup", $full_name_no_cleanup, array("placeholder"=>"Full Name No Cleanup"));
    // if input: " <b>Joe</b> <li>Smith</i> ", then output: <input type="text" name="full_name_no_cleanup" value=" &lt;b&gt;Joe&lt;/b&gt; &lt;li&gt;Smith&lt;/i&gt; " placeholder="Full Name No Cleanup">
    // * note that attribute values are HTML encoded in HTML code, the input box will display " <b>Joe</b> <li>Smith</i> " in the browser
    echo "<div>value=\"".htmlspecialchars($full_name_no_cleanup)."\"</div>";
    ?></div>

    <?php
    // return to default value
    $form->setDoPassedStringCleanup(true);
    ?>

    <br><br><br>


    <!--
    setDoSelectOptionValueEqualsText()
    -->
    <h2>setDoSelectOptionValueEqualsText()</h2>
    <ul>
        <li>when an array of data is passed for the options of a dropdown menu (select),
            determines whether the value for each option is the array item key or
            the array item value (same as the displayed text)</li>
        <li>true or false (boolean); default = <b>false</b></li>
    </ul>
    <?php
    // set data for both dropdown menus (select)
    $selectOptions = array(''=>'-no answer-', 'day'=>'Daytime', 'night'=>'Nighttime');
    ?>
    <div class="section-label">doSelectOptionValueEqualsText is set to <b>true</b>. Option values will be array item
        values (same as the displayed text).</div>
    <div><?php 
        $form->setDoSelectOptionValueEqualsText(true);
        $form->select("time_of_day", $selectOptions, "");
        // output: <select name="time_of_day"><option value="-no answer-">-no answer-</option><option value="Daytime">Daytime</option><option value="Nighttime">Nighttime</option></select>
    ?></div>

    <div class="section-label">doSelectOptionValueEqualsText is set to <b>false</b>. Option values will be array item
        keys.</div>
    <div><?php
        $form->setDoSelectOptionValueEqualsText(false);
        $form->select("time_of_day", $selectOptions, "");
        // output: <select name="time_of_day"><option value="">-no answer-</option><option value="day">Daytime</option><option value="night">Nighttime</option></select>
    ?></div>



    <br><br><br>

    <div><?php $form->submit('Submit the form'); ?></div>

    <?php $form->formEnd(); ?>

</body>

</html>