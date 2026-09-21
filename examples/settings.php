<?php

// include the class file
require_once('../FormHelper.php');

$form = new FormHelper();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML Form Example - Settings</title>
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <h1>HTML Form Example - Settings</h1>
    <div><a href="./">&laquo; back to All Examples</a></div><br>

    <p><b>* note that some examples require form submission and/or viewing source code to illustrate the functionality</b></p>

    <?php
    $form->formStart();
    ?>

    <br>

    <div><?php $form->submit('Submit the form'); ?></div><br>

    <h2>exitProgramOnFailure</h2>
    <ul>
        <li>end program on settings/configuration error?</li>
        <li>helpful for development, should be false in production</li>
        <li>true or false (boolean); default = <b>false</b></li>
    </ul>

    <div class="section-label">exitProgramOnFailure is set to <b>false</b>. invalid setting will NOT end program.</div>
    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        $form->updateSetting('exitProgramOnFailure', false);
        // setting an invalid parameter
        $form->updateSetting('settingDoesNotExist', true);
        // output: none. the invalid setting does nothing. the page continues loading
        echo "\n\n";
        ?>
    </div>

    <div class="section-label">exitProgramOnFailure is set to <b>true</b>. upon an invalid setting, output error and end program.</div>
    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        $form->updateSetting('exitProgramOnFailure', true);
        // setting an invalid parameter
        // * note that this code is commented out because the settings page would end if the code ran. 
        // $form->updateSetting('settingDoesNotExist', true);
        // output: <div>ERROR: updateSetting function received invalid setting: settingDoesNotExist</div>
        echo "\n\n";
        ?>
    </div>



    <br><br><br>



    <h2>addIdAttributeFromName</h2>
    <ul>
        <li>automatically add an "id" attribute with the same value as "name"?</li>
        <li>does not affect radio inputs because they can have multiple elements with the same "name" attribute</li>
        <li>does not affect buttons because the class does not automatically add a name "attribute" to buttons</li>
        <li>if false, id attributes can be added with the $moreAttributes parameter</li>
        <li>if true, id attributes can be overridden with the $moreAttributes parameter</li>
        <li>true or false (boolean); default = <b>false</b></li>
    </ul>

    <div class="section-label">addIdAttributeFromName is set to <b>false</b>. id is not passed. no id attribute will
        be on the tag.</div>
    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        $form->updateSetting('addIdAttributeFromName', false);
        $form->text("name1a");
        // output: <input type="text" name="name1a" value="">
        echo "\n\n";
        ?>
    </div>

    <div class="text-label">addIdAttributeFromName is set to <b>true</b>. The id is automatically set to the same
        value as name (parameter 1).</div>
    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        $form->updateSetting('addIdAttributeFromName', true);
        $form->text("name1b");
        // output: <input type="text" name="name1b" value="" id="name1b">
        echo "\n\n";
        ?>
    </div>

    <div class="section-label">addIdAttributeFromName is still to <b>true</b>. But an id is passed through the
        $moreAttributes parameter, overwriting the automatic value.</div>
    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        $form->text("name1c", "", array("id" => "name1c_overwrite"));
        // output: <input type="text" name="name1c" value="" id="name1c_overwrite">
        echo "\n\n";
        ?>
    </div>

    <?php
    // set to false (default value) to stop html errors for duplicate id's on this page.
    $form->updateSetting('addIdAttributeFromName', false);
    ?>


    <br><br><br>



    <h2>selectOptionValueEqualsDisplayText</h2>
    <ul>
        <li>in a select (dropdown), use each option's display text as its value</li>
        <li>if false, the passed options parameter should be an associative
            array: $options = array('blue'=>'Blue', 'light_green'=>'Light Green');</li>
        <li>if true, the passed options parameter can be an indexed (non-associative)
            array since the key is ignored: $options = array('Blue', 'Light Green');</li>
        <li>true or false (boolean); default = <b>false</b></li>
    </ul>
    <?php
    // set data for both dropdown menus (select)
    $selectOptions = array('' => '-no answer-', 'baseball' => 'Daytime', 'night' => 'Nighttime');
    ?>
    <div class="section-label">selectOptionValueEqualsDisplayText is set to <b>false</b>. Option values will be array item keys.</div>
    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        echo '';
        $form->updateSetting('selectOptionValueEqualsDisplayText', false);
        $form->select("time_of_day_1", $selectOptions, "");
        // output: <select name="time_of_day_1"><option value="">-no answer-</option><option value="day">Daytime</option><option value="night">Nighttime</option></select>
        echo "\n\n";
        ?>
    </div>

    <div class="section-label">selectOptionValueEqualsDisplayText is set to <b>true</b>. Option values will be array item values (same as the displayed text).</div>
    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        $form->updateSetting('selectOptionValueEqualsDisplayText', true);
        $form->select("time_of_day_2", $selectOptions, "");
        // output: <select name="time_of_day_2"><option value="-no answer-">-no answer-</option><option value="Daytime">Daytime</option><option value="Nighttime">Nighttime</option></select>
        echo "\n\n";
        ?>
    </div>

    <br><br><br>


    <h2>passedTrim</h2>
    <ul>
        <li>trim whitespace from the beginning and end of passed values</li>
        <li>used in the getPassed() function</li>
        <li>true or false (boolean); default = <b>true</b></li>
    </ul>

    <div><?php echo $form->text('trim_test', ' My Text ', array('readonly')); ?></div>

    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        $form->updateSetting('passedTrim', false);
        echo "set to <b>false</b>: ";
        var_dump($form->getPassed("trim_test"));
        // output if trim_test with value of " My Text " was passed: string(9) " My Text " 
        echo "\n\n";
        ?>
    </div>

    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        $form->updateSetting('passedTrim', true);
        echo "set to <b>true</b>: ";
        var_dump($form->getPassed("trim_test"));
        echo "\n\n";
        // output if trim_test with value of " My Text " was passed: string(7) "My Text" 
        ?>
    </div>



    <br><br><br>



    <h2>passedStripTags</h2>
    <ul>
        <li>remove HTML tags and script/style blocks from passed values</li>
        <li>used in the getPassed() function</li>
        <li>true or false (boolean); default = <b>true</b></li>
    </ul>

    <div><?php echo $form->text('strip_tags_test', '<i><b>My Text</b></i>', array('readonly')); ?></div>
    <div>
        <?php
        echo "set to <b>false</b>: ";
        echo "\n\n<!-- class output: -->\n";

        $form->updateSetting('passedStripTags', false);
        echo $form->getPassed("strip_tags_test");
        // output if strip_tags_test with value of "<i><b>My Text</b></i>" was passed: <i><b>My Text</b></i>
        echo "\n\n";
        ?>
    </div>

    <div>
        <?php
        echo "set to <b>true</b>: ";
        echo "\n\n<!-- class output: -->\n";
        $form->updateSetting('passedStripTags', true);
        echo $form->getPassed("strip_tags_test");
        // output if strip_tags_test with value of "<i><b>My Text</b></i>" was passed: My Text
        echo "\n\n";
        ?>
    </div>


    <br><br><br>



    <h2>passedConvertToStandardCharacters</h2>
    <ul>
        <li>converts non-standard (non-ASCII) characters in passed values</li>
        <li>used in the getPassed() function</li>
        <li>replaces characters with equivalents when possible, otherwise replaces the character with a dash</li>
        <li>true or false (boolean); default = <b>false</b></li>
    </ul>

    <div>
        <?php
        $testString = // Deja vu with accents over the e and the a
            'D' .
            chr(195) . chr(169) . // e with accent
            'j' .
            chr(195) . chr(160) . // a with accent
            ' vu';
        $form->text('convert_test', $testString, array('readonly'));
        ?>
    </div>

    <div>
        <?php
        $form->updateSetting('passedConvertToStandardCharacters', false);
        echo "set to <b>false</b>: ";
        echo "\n\n<!-- class output: -->\n";
        echo $form->getPassed("convert_test");
        // output of convert_test with string containing non-standard characters: (maintain special characters)
        echo "\n\n";
        ?>
    </div>

    <div>
        <?php
        $form->updateSetting('passedConvertToStandardCharacters', true);
        echo "set to <b>true</b>: ";
        echo "\n\n<!-- class output: -->\n";
        echo $form->getPassed("convert_test");
        // output of convert_test with string containing non-standard characters: (replace or remove special characters)
        echo "\n\n";
        ?>
    </div>


    <br><br><br>


    <h2>returnNullIfUnavailable</h2>
    <ul>
        <li>when retrieving a passed value, return NULL when variable is not available (not set or invalid)</li>
		<li>used in the getPassed() function</li>
        <li>by default, when a variable is not set, the return value is "" (empty string), 0, or an empty array depending on if a flag is set to return
              as an int, float, or array. if returnNullIfUnavailable is set to true, null will be returned instead</li>
        <li>will also return NULL when a variable doesn't match the settings. for instance, the 'array' flag is set, but the value is not an array</li>
        <li>true or false (boolean); default = <b>false</b></li>
    </ul>

    <div class="section-label">returnNullIfUnavailable is set to <b>false</b>..</div>
    <div>
        <?php
        $form->updateSetting('returnNullIfUnavailable', false);
        echo "set to <b>false</b>: ";
        echo "\n<!-- class output: -->\n";
        $value = $form->getPassed('variable_is_not_set');
        var_dump($value);
        // output: string(0) ""
        echo "\n\n";
        ?>
    </div>

    <div class="section-label">returnNullIfUnavailable is set to <b>true</b>.</div>
    <div>
        <?php
        $form->updateSetting('returnNullIfUnavailable', true);
        echo "set to <b>true</b>: ";
        echo "\n<!-- class output: -->\n";
        $value = $form->getPassed('variable_is_not_set');
        var_dump($value);
        // output: NULL
        echo "\n\n";
        ?>
    </div>



    <br><br><br>



    <h2>returnHtml</h2>
    <ul>
        <li>return the HTML elements as a string?</li>
        <li>true or false (boolean); default = <b>false</b></li>
    </ul>

    <div class="section-label">returnHtml is set to <b>false</b>. HTML is directly output, echo is <b>not</b> required.</div>
    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        $form->updateSetting('returnHtml', false);
        $form->text("name3a");
        // output: <input type="text" name="name3a" value="">
        echo "\n\n";
        ?>
    </div>

    <div class="text-label">returnHtml is set to <b>true</b>. HTML is returned. <b>echo</b> is required.</div>
    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        $form->updateSetting('returnHtml', true);

        echo $form->text("name3b");
        // output: <input type="text" name="name3b" value="">

        echo "\n<br>\n<br>";

        $html = $form->text("name3c");
        echo $html;
        // output: <input type="text" name="name3c" value="">
        echo "\n\n";
        ?>
    </div>


    <?php
    // set to false (default value) for direct output on the rest of the settings page
    $form->updateSetting('returnHtml', false);
    ?>


    <br><br><br>



    <h2>xhtmlStyleOutput</h2>
    <ul>
        <li>output HTML as XHTML-style syntax?</li>
        <li>closes self-closing elements and boolean attributes (selected, readonly, etc)
            will have values that match the attribute</li>
        <li>true or false (boolean); default = <b>false</b></li>
    </ul>
    <div class="section-label">xhtmlStyleOutput is set to <b>false</b>.</div>
    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        $form->updateSetting('xhtmlStyleOutput', false);
        $form->text("name4a", "", array("readonly"));
        // output: <input type="text" name="name4a" value="" readonly>
        echo "\n\n";
        ?>
    </div>

    <div class="section-label">xhtmlStyleOutput is set to <b>true</b>.</div>
    <div>
        <?php
        echo "\n<!-- class output: -->\n";
        $form->updateSetting('xhtmlStyleOutput', true);
        $form->text("name4b", "", array("readonly"));
        // output: <input type="text" name="name4b" value="" readonly="readonly" />
        echo "\n\n";
        ?>
    </div>

    <?php
    // set to false (default value)
    $form->updateSetting('xhtmlStyleOutput', false);
    ?>


    <br><br><br>



    <div><?php $form->submit('Submit the form'); ?></div>

    <?php $form->formEnd(); ?>

</body>

</html>