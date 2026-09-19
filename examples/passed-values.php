<?php

// include the class file
require_once('../FormHelper.php');

$form = new FormHelper();

// set a cookie for the cookie example
if (!isset($_COOKIE['getPassed_cookie_test'])) {
    setcookie('getPassed_cookie_test', 'Cookie Value', time() + 3600);
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML Form Example - getPassed</title>
    <link rel="stylesheet" href="./style.css?z=1">
</head>

<body>
    <h1>HTML Form Example - getPassed</h1>
    <div><a href="./">&laquo; back to All Examples</a></div>

    <p>
        Processing forms generally requires handling passed from POST or GET. Check that the data exists, set it to a value, and
        manipulate it. This class does all of this using the <b>getPassed()</b> function.
    </p>

    <p>
        Note that this class focuses on displaying forms and retrieving data. It is <b>not</b> focused on validation (checking that required fields are entered that strings are formatted correctly) or processing (send emails from mail form or saving data to a database).
    </p>

    <h2>Related Settings</h2>

    <p>These settings determine how passed values are pre-processed in the class.</p>

    <h3>passedTrim</h3>
    <ul>
        <li>trim whitespace from the beginning and end of passed values</li>
        <li>false: " Joe Smith " is unchanged</li>
        <li>true: " Joe Smith " is converted to "Joe Smith"</li>
        <li>example: $form->updateSetting('passedTrim', false);</li>
        <li>true or false (boolean); default = <b>true</b></li>
    </ul>

    <h3>passedStripTags</h3>
    <ul>
        <li>remove HTML tags and script/style blocks from passed values</li>
        <li>false: "<?php echo htmlspecialchars('<b><i>Joe Smith</i></b>'); ?>" is unchanged</li>
        <li>true: "<?php echo htmlspecialchars('<b><i>Joe Smith</i></b>'); ?>" is converted to "Joe Smith"</li>
        <li>example: $form->updateSetting('passedStripTags', false);</li>
        <li>true or false (boolean); default = <b>true</b></li>
    </ul>

    <h3>passedConvertToStandardCharacters</h3>
    <ul>
        <li>converts non-standard characters from passed values</li>
        <li>replaces characters with equivalents when possible, otherwise replaces the character with a dash</li>
        <li>false: "© Déjà vu" is unchanged</li>
        <li>true: "© Déjà vu" is converted to "- Deja vu"</li>
        <li>example: $form->updateSetting('passedConvertToStandardCharacters', true);</li>
        <li>true or false (boolean); default = <b>false</b></li>
    </ul>

    <h3>exitProgramOnFailure</h3>
    <ul>
        <li>end program on settings/configuration error?</li>
        <li>helpful for development, should be false in production</li>
        <li>completely stops the page from loading, not just the form</li>
        <li>for instance, if true, setting flags for a variable to be returned as both POST and GET will output a warning and end the program</li>
        <li>example: $form->updateSetting('exitProgramOnFailure', true);</li>
        <li>true or false (boolean); default = <b>false</b></li>
    </ul>


    <br>

    <h2>Functions</h2>

    <h3>getPassed($variable, $flags)</h3>
    <ul>
        <li>get variables passed by POST (form), GET (query string), or COOKIE</li>
        <li>$flags are a list of flags that determine where the data comes from and how it is processed.</li>
        <li>a flag can be passed to specify where to retrieve the variable. <b>post</b>, <b>get</b>, or <b>cookie</b></li>
        <li>if no post, get, or cookie flag is passed, $_POST will be checked. if not set, $_GET will be checked. $_COOKIE will <b>NOT</b> be checked.</li>
        <li>checks that the variable exists, so it will not produce a PHP warning if the variable is not set</li>
        <li>$flags can be passed as an array or a string separated by commas or spaces. ex:
            $flags = array('post', 'float'); or $flags = "post float"; or $flags = "post,float";</li>
    </ul>

    <h3>getPost($variable, $flags)</h3>
    <ul>
        <li>get variables passed by POST (form)</li>
        <li>shorthand for calling getPassed with a "post" flag</li>
        <li>getPassed("name", "post") is the same as getPost("name")</li>
    </ul>

    <h3>getGet($variable, $flags)</h3>
    <ul>
        <li>get variables passed by GET (URL query string parameters) </li>
        <li>shorthand for calling getPassed with a "get" flag</li>
        <li>getPassed("name", "get") is the same as getGet("name")</li>
    </ul>


    <br>

    <h2>flags</h2>

    <ul>
        <li><b>post</b> - retrieve the variable from POST only</li>
        <li><b>get</b> - retrieve the variable from GET only</li>
        <li><b>cookie</b> - retrieve the variable from COOKIE only. note that COOKIE values are retrievable since they can be processed along with form data. For instance when
            saving form data to a database or processing an email form, a COOKIE value can be checked to determine if the user is logged in and that info can be processed.</li>
        <li><b>int</b> - convert retrieved value to an integer</li>
        <li><b>float</b> - convert retrieved value to a float</li>
        <li><b>array</b> - process value as an array, can be used with int or float to process an array of integers or floats</li>
        <li><b>strip-tags</b>, <b>no-strip-tags</b> - override the "passedStripTags" setting. see setting for details</li>
        <li><b>trim</b>, <b>no-trim</b> - override the "passedTrim" setting. see setting for details</li>
        <li><b>convert</b>, <b>no-convert</b> - override the "passedConvertToStandardCharacters" setting. see setting for details</li>
    </ul>



    <h2>Form processing examples</h2>

    <h3>POST form</h3>

    <p>Submit form and scroll to the bottom of the page to see how POST variables are retrieved using the class</p>

    <div>
        <?php $form->formStart($_SERVER['PHP_SELF'].'#form_output', 'post'); ?>

        <?php $form->hidden('post_submitted', '1'); ?>

        <div><label>Basic Text</label></div>
        <?php $form->text('basic_test', 'My Text'); ?>

        <br><br>
        <div><label>Number Text</label></div>
        <?php $form->text('number_test', '5.30'); ?>

        <br><br>
        <div><label>Array (test_array) (note that C is not checked by default) </label></div>
        <?php $form->checkbox('test_array[]', true, 'A'); ?>A &nbsp;
        <?php $form->checkbox('test_array[]', true, 'B'); ?>B &nbsp;
        <?php $form->checkbox('test_array[]', false, 'C'); ?>C &nbsp;
        <?php $form->checkbox('test_array[]', true, 'D'); ?>D &nbsp;

        <br><br>
        <div><label>Text With Whitespace</label></div>
        <?php $form->text('whitespace_test', '     My Text     '); ?>

        <br><br>
        <div><label>Text With HTML tags</label></div>
        <?php $form->text('tags_test', '<b><i>My Text</i></b>'); ?>

        <br><br>
        <div><label>Text With non-standard (non-ASCII) characters</label></div>
        <?php
        $stringWithSpecialCharacters = // Deja vu with accents over the e and the a
            'D' .
            chr(195) . chr(169) . // e with accent
            'j' .
            chr(195) . chr(160) . // a with accent
            ' vu';
        $form->text('non_standard_test', $stringWithSpecialCharacters); ?>

        <br><br><?php $form->button("Submit POST form"); ?>

        <?php $form->formEnd(); ?>

    </div>


    <h3>GET form</h3>

    <p>Submit form and scroll to the bottom of the page to see how GET variables are retrieved using the class</p>

    <div>
        <?php $form->formStart($_SERVER['PHP_SELF'].'#form_output', 'get'); ?>

        <?php $form->hidden('get_submitted', '1'); ?>

        <div><label>Basic Text (basic_test)</label></div>
        <?php $form->text('basic_test', 'My Text'); ?>

        <br><br>
        <div><label>Number Text (number_test)</label></div>
        <?php $form->text('number_test', '5.30'); ?>

        <br><br>
        <div><label>Array (test_array) (note that C is not checked by default) </label></div>
        <?php $form->checkbox('test_array[]', true, 'A'); ?>A &nbsp;
        <?php $form->checkbox('test_array[]', true, 'B'); ?>B &nbsp;
        <?php $form->checkbox('test_array[]', false, 'C'); ?>C &nbsp;
        <?php $form->checkbox('test_array[]', true, 'D'); ?>D &nbsp;

        <br><br>
        <div><label>Text With Whitespace (whitespace_test)</label></div>
        <?php $form->text('whitespace_test', '     My Text     '); ?>

        <br><br>
        <div><label>Text With HTML tags (tags_test)</label></div>
        <?php $form->text('tags_test', '<b><i>My Text</i></b>'); ?>

        <br><br>
        <div><label>Text With non-standard (non-ASCII) characters</label></div>
        <?php
        $stringWithSpecialCharacters = // Deja vu with accents over the e and the a
            'D' .
            chr(195) . chr(169) . // e with accent
            'j' .
            chr(195) . chr(160) . // a with accent
            ' vu';
        $form->text('non_standard_test', $stringWithSpecialCharacters); ?>

        <br><br><?php $form->button("Submit GET Form"); ?>

        <?php $form->formEnd(); ?>

    </div>



    <?php if ($form->getPassed('post_submitted') || $form->getPassed('get_submitted')) { ?>
        
        <div id="form_output"></div>

        <?php if ($form->getPassed('post_submitted')) { ?>
            <h2>POST form submitted</h2>
            <pre><?php var_dump($_POST); ?></pre>
        <?php } else { ?>
            <h2>GET form submitted</h2>
            <pre><?php var_dump($_GET); ?></pre>
        <?php } ?>

        <table>
            <tr>
                <th>Description</th>
                <th>Code</th>
                <th>Processed Output</th>
                <th>Notes</th>
            </tr>

            <tr>
                <td>retrieve basic_test value</td>
                <td>$form->getPassed('basic_test');</td>
                <td><?php var_dump($form->getPassed('basic_test')); ?></td>
                <td></td>
            </tr>

            <tr>
                <td>retrieve number_test value</td>
                <td>$form->getPassed('number_test');</td>
                <td><?php var_dump($form->getPassed('number_test')); ?></td>
                <td></td>
            </tr>
            <tr>
                <td>retrieve number_test as a float</td>
                <td>$form->getPassed('number_test', 'float');</td>
                <td><?php var_dump($form->getPassed('number_test', 'float')); ?></td>
                <td></td>
            </tr>
            <tr>
                <td>retrieve number_test as an integer</td>
                <td>$form->getPassed('number_test', 'int');</td>
                <td><?php var_dump($form->getPassed('number_test', 'int')); ?></td>
                <td></td>
            </tr>

            <tr>
                <td>retrieve test_array as an array</td>
                <td>$form->getPassed('test_array', 'array');</td>
                <td><?php var_dump($form->getPassed('test_array', 'array')); ?></td>
                <td>by default A, B, and D are set in the form (C is skipped)</td>
            </tr>

            <tr>
                <td>retrieve whitespace_test value</td>
                <td>$form->getPassed('whitespace_test', 'trim');</td>
                <td><?php var_dump($form->getPassed('whitespace_test', 'trim')); ?></td>
                <td>white space is removed (default behavior, <b>trim</b> flag only needed if <b>passedTrim</b> is set to false)</td>
            </tr>
            <tr>
                <td>retrieve whitespace_test with <b>no-trim</b> flag</td>
                <td>$form->getPassed('whitespace_test', 'no-trim');</td>
                <td><?php var_dump($form->getPassed('whitespace_test', 'no-trim')); ?></td>
                <td>white space is retained</td>
            </tr>

            <tr>
                <td>retrieve tags_test value</td>
                <td>$form->getPassed('tags_test', 'strip-tags');</td>
                <td><?php var_dump($form->getPassed('tags_test', 'strip-tags')); ?></td>
                <td>tags are removed (default behavior, <b>strip-tags</b> flag only needed if <b>passedStripTags</b> is set to false)</td>
            </tr>
            <tr>
                <td>retrieve tags_test with <b>no-strip-tags</b> flag</td>
                <td>$form->getPassed('tags_test', 'no-strip-tags');</td>
                <td><?php var_dump($form->getPassed('tags_test', 'no-strip-tags')); ?></td>
                <td>tags are retained. note that tags are directly output if they are not escaped, so the text will appear bold and in italic in the browser</td>
            </tr>

            <tr>
                <td>retrieve non_standard_test value</td>
                <td>$form->getPassed('non_standard_test', 'no-convert');</td>
                <td><?php var_dump($form->getPassed('non_standard_test', 'no-convert')); ?></td>
                <td>non-standard (non-ASCII) characters are retained (default behavior, <b>no-convert</b> flag only needed if <b>passedConvertToStandardCharacters</b> is set to true)</td>
            </tr>
            <tr>
                <td>retrieve non_standard_test with <b>convert</b> flag</td>
                <td>$form->getPassed('non_standard_test', 'no-trim');</td>
                <td><?php var_dump($form->getPassed('non_standard_test', 'convert')); ?></td>
                <td>non-standard (non-ASCII) characters are replaced or removed</td>
            </tr>

            <tr>
                <td>set multiple flags by array</td>
                <td>$form->getPassed('number_test', array('post', 'int'));</td>
                <td><?php var_dump($form->getPassed('number_test', array('post', 'int'))); ?></td>
                <td>will get from POST and convert to an integer. will be 0 (default value) if the GET form is submitted</td>
            </tr>
            <tr>
                <td>set multiple flags by string</td>
                <td>$form->getPassed('number_test', 'post,int');</td>
                <td><?php var_dump($form->getPassed('number_test', 'post,int')); ?></td>
                <td>will get from GET and convert to an integer. will be 0 (default value) if the GET form is submitted. commas and spaces are both
                    separators, so "post,int", "post int", and "post, int" are equivalent</td>
            </tr>

            <tr>
                <td>retrieve basic_test via POST</td>
                <td>$form->getPassed('basic_test', 'post');</td>
                <td><?php echo $form->getPassed('basic_test', 'post'); ?></td>
                <td>value set for POST form submit, blank for GET</td>
            </tr>
            <tr>
                <td>retrieve basic_test using getPost function</td>
                <td>$form->getPost('basic_test');</td>
                <td><?php echo $form->getPost('basic_test'); ?></td>
                <td>value set for POST form submit, blank for GET. identical to calling getPassed with 'post' flag</td>
            </tr>
            <tr>
                <td>retrieve basic_test via GET</td>
                <td>$form->getPassed('basic_test', 'get');</td>
                <td><?php echo $form->getPassed('basic_test', 'get'); ?></td>
                <td>value set for GET form submit, blank for POST</td>
            </tr>
            <tr>
                <td>retrieve basic_test using getGet function</td>
                <td>$form->getGet('basic_test');</td>
                <td><?php echo $form->getGet('basic_test'); ?></td>
                <td>value set for GET form submit, blank for POST identical to calling getPassed with 'get' flag</td>
            </tr>
            <tr>
                <td>retrieve basic_test via COOKIE</td>
                <td>$form->getPassed('basic_test', 'cookie');</td>
                <td><?php echo $form->getPassed('basic_test', 'cookie'); ?></td>
                <td>blank for this example because no COOKIE value named basic_test is set; the cookie flag retrieves only the COOKIE value.</td>
            </tr>
        </table>
    <?php } ?>


</body>

</html>