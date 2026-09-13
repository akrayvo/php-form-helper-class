# PHP Form Helper Class
[View on GitHub »](https://github.com/akrayvo/php-form-helper-class)

A simple class to display HTML form elements using PHP. 

It takes care of the HTML syntax and encoding when generating form elements. It does **not** handle all HTML (labels, line breaks, etc.), validation, or data processing.

## Requirements
* PHP >= 5.1

## Installation
Add the **FormHelper.php** file to your project.

## Basic Example
HTML / PHP code
```
<?php

// include the class file
require_once('../FormHelper.php');
// initialize class
$form = new FormHelper();

// get the value passed to the page. check both $_POST and $_GET
$name = $form->getPassed('name');

// hard-coded options for a select (dropdown menu) field
// in actual usage, this data could also come from a database or data file
$colors = array(
    '' => '- select a color -',
    'blue' => 'Blue',
    'green' => 'Green',
    'lightBlue' => 'Light Blue',
    'red' => 'Red');
?>

<?php 
// start the form <form>
$form->formStart(); 
?>

    <div><label>Name</label></div>    
    <?php
    // text input <input type="text">
    $form->text('name', $name);
    ?><br><br>
    
    <div><label>Favorite Color</label></div>
    <?php 
    // select (dropdown) with options <select><option>
    $form->select('colors', $colors);
    ?><br><br>
    
    <div><label>Comments</label></div>
    <?php 
    // textarea (large text input) <textarea>
    $form->textarea('comments');
    ?><br><br>
    
    <?php 
    // submit button <input type="submit">
    $form->submit('Save Info');
    ?>

<?php 
// end the form </form>
$form->formEnd(); ?>
```
Generated HTML
```
<form action="/yourPage.php" method="post">
    <div><label>Name</label></div>    
    <input type="text" name="name" value=""><br><br>
    
    <div><label>Favorite Color</label></div>
    <select name="colors"><option value="">- select a color -</option><option value="blue">Blue</option><option value="green">Green</option><option value="lightBlue">Light Blue</option><option value="red">Red</option></select><br><br>
    
    <div><label>Comments</label></div>
    <textarea name="comments"></textarea><br><br>
    
    <input type="submit" name="submitInputButton" value="Save Info">
</form>
```

## Using the class vs. standard PHP/HTML

### Getting values from the form and cleanup (trimming and removing HTML tags)

without class
```
<?php
$full_name = "";
if (isset($_POST['full_name'])) {
    $full_name = $_POST['full_name'];
    $full_name = strip_tags($full_name);
    $full_name = trim($full_name);
}
?>
```
with class
```
<?php
$full_name = $form->getPassed('full_name');
?>
```

### Form start and end

without class
```
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
</form>
```

with class
```
<?php
$form->formStart();
$form->formEnd();
?>
```

### Text input

without class
```
<input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($full_name); ?>" placeholder="Full Name">
```

with class - if doAddIdAttributeFromName is set to true - $form->setDoAddIdAttributeFromName(true);
```
<?php $form->text("full_name", $full_name, array("placeholder"=>"Full Name")); ?>
```

### Dropdown menu (select)

without class
```
<select name="color">
    <option value="" <?php if ($color == "") { echo "selected"; } ?>></option>
    <option value="red" <?php if ($color == "red") { echo "selected"; } ?>>red</option>
    <option value="blue" <?php if ($color == "blue") { echo "selected"; } ?>>blue</option>
    <option value="red &amp; blue" <?php if ($color == "red & blue") { echo "selected"; } ?>>red &amp; blue</option>
</select>
```

with class
```
<?php
$colors = array(""=>"", "red"=>"red", "blue"=>"blue", "red & blue"=>"red & blue");
$form->select('color', $colors, $color);
?>
```
### Checkbox

without class
```
<input type="checkbox" name="is_checked" value="1" <?php if ($is_checked) { echo "checked"; } ?>>
```

with class
```
<?php
$form->checkbox('is_checked', $is_checked);
?>
```

## Settings

### private $doAddIdAttributeFromName = false;
* automatically add an `id` attribute with the same value as the `name` attribute
* does not affect radio inputs because they can have multiple elements with the same name attribute
* does not affect buttons because the button() function does not have a name parameter
* if set to false, `id` can be added with the `$moreAttributes` parameter
* if set to true, `id` can be overridden with the `$moreAttributes` parameter 
```
// $doAddIdAttributeFromName = false | parameter not passed, no id attribute
$form->setDoAddIdAttributeFromName(false);
$form->text('first_name');
// <input type="text" name="first_name" value="">

// $doAddIdAttributeFromName = false | parameter passed, id set
$form->setDoAddIdAttributeFromName(false);
$form->text("first_name", "", array("id"=>"first_name"));
// <input type="text" name="first_name" value="" id="first_name">

// $doAddIdAttributeFromName = true | id automatically added
$form->setDoAddIdAttributeFromName(true);
$form->text("first_name");
// <input type="text" name="first_name" value="" id="first_name">

// $doAddIdAttributeFromName = true | auto id overridden by passed parameter
$form->setDoAddIdAttributeFromName(true);
$moreAttributes = array('id'=>'f_name');
$form->text('first_name', '', $moreAttributes);
// <input type="text" name="first_name" value="" id="f_name">
```

### private $doReturnHtml = false;
* return the HTML elements as a string
* if false, output is output directly (no echo required commend)
```
// $doReturnHtml = false | echo is not required
$form->setDoReturnHtml(false);
$form->text("first_name");
// <input type="text" name="first_name" value="">

// $doReturnHtml = true | echo is required
$form->setDoReturnHtml(true);
$html = $form->text("first_name");
// (outputs nothing)
echo $html;
// <input type="text" name="first_name" value="">
```
     
### private $isXhtmlStyle = false;
* output XHTML-style HTML
* use a closing slash on HTML tags that do not require a closing tag, ex: `<input type="text" name="name">` vs `<input type="text" name="name" />`
* add an attribute name for attributes that do not require them, ex: `<button disabled>Submit</button>` vs. `<button disabled="disabled">Submit</button>` vs. 
* note that this should generally be false, but can be set to true for consistency with existing code 
```
// $isXhtmlStyle = false
$form->setIsXhtmlStyle(false);
$form->text('first_name', '', ['readonly']);
// <input type="text" name="first_name" value="" readonly>

// $isXhtmlStyle = true
$form->setIsXhtmlStyle(true);
$form->text('first_name', '', ['readonly']);
// <input type="text" name="first_name" value="" readonly="readonly" />
```

### private $doPassedStringCleanup = true;
* string cleanup of passed variables
* removes HTML tags (strip_tags)
* strips whitespace from the beginning and end of a string (trim)
* used in `getPost()`, `getGet()`, and `getPassed()` functions
```
// passed from form: $first_name = "<b>Joe</b>"

$form->setDoPassedStringCleanup(true);
$first_name = $form->getPassed('first_name');
$form->text('first_name', $first_name);
// <input type="text" name="first_name" value="Joe">

$form->setDoPassedStringCleanup(false);
$first_name = $form->getPassed('first_name');
$form->text('first_name', $first_name);
// (note that the value is HTML encoded)
// <input type="text" name="first_name" value="&lt;b&gt;Joe&lt;/b&gt;">
```

### private $doSelectOptionValueEqualsText = false;
* when an array of data is passed for the options of a dropdown menu (select), this determines if the value for each option is the array item key or the array item value (same as the display)
* if set, `select` `option` `value` and display text will both be set to the options array item value, so `[2=>'a', => 3=>'b']` will output `<option value="a">a</option><option value="b">b</option>`
* if NOT set, `select` `option` `value` will be the array key and the display text will be the array value, so `[2=>'a', => 3=>'b']` will output `<option value="2">a</option><option value="3">b</option>`
```
$options = array('NY'=>'New York', 'OH'=>'Ohio');

$form->setDoSelectOptionValueEqualsText(false);
$form->select('state', $options);
// <select name="state"><option value="NY">New York</option><option value="OH">Ohio</option></select>

$form->setDoSelectOptionValueEqualsText(true);
$form->select('state', $options);
// <select name="state"><option value="New York">New York</option><option value="Ohio">Ohio</option></select>
```

## Using form tag attributes
All form element functions include a `$moreAttributes` parameter. It takes an array of attributes with the $key as the attribute name and the value being the value.

If the key is numeric, it will be handled as a boolean attribute (with no value such as `readonly`, `disabled`, `checked`, etc.).

Common attributes would include `id`, `class`, `style`, `placeholder`, etc.

```
$moreAttributes = array('style'=>'padding:20px;', 'placeholder'=>'Name', 'readonly');
$form->text('name', '', $moreAttributes);
```
HTML output
```
<input type="text" name="name" value="" style="padding:20px" placeholder="Name" readonly>
```

## Functions

### Settings
* `setDoAddIdAttributeFromName($value)` - set $doAddIdAttributeFromName
* `setDoReturnHtml($value)` - set $doReturnHtml
* `setIsXhtmlStyle($value)` - set $isXhtmlStyle
* `setDoPassedStringCleanup($value)` - set $doPassedStringCleanup
* `setDoSelectOptionValueEqualsText($value)` - set $doSelectOptionValueEqualsText
### String Manipulation
* `htmlEscape($string)` - escape a string to display in HTML
* `stringCleanup($string)` - strips HTML tags from a string
### Get Passed Data
* `getPassed($var, $returnOnfail = '')` - retrieve a value from $_GET or $_POST
* `getPost($var, $returnOnfail = '')` - retrieve a value from $_POST
* `getGet($var, $returnOnfail = '')` - retrieve a value from $_GET
### input elements &lt;input&gt;
* `hidden($name, $value = '', $moreAttributes = array())` - `<input type="hidden">`
* `text($name, $value = '', $moreAttributes = array())` - `<input type="text">`
* `color($name, $value = '', $moreAttributes = array())` - `<input type="color">`
* `number($name, $value = '', $moreAttributes = array())` - `<input type="number">`
* `range($name, $min, $max, $value = '', $moreAttributes = array())` - `<input type="range">`
* `email($name, $value = '', $moreAttributes = array())` - `<input type="email">`
* `tel($name, $value = '', $moreAttributes = array())` - `<input type="tel">`
* `date($name, $value = '', $moreAttributes = array())` - `<input type="date">`
* `password($name, $moreAttributes = array())` - `<input type="password">`
* `checkbox($name, $isChecked = false, $value = 1, $moreAttributes = array())` - `<input type="checkbox">`
* `radio($name, $value, $selectedValue = '', $moreAttributes = array())` - `<input type="radio">`
* `submit($value = '', $name = '',  $moreAttributes = array())` - `<input type="submit">`
* `reset($value = '', $name = '',  $moreAttributes = array())` - `<input type="reset">`

### Other form elements
* `formStart($action = '', $method = '', $moreAttributes = array())` - `<form>`
* `formEnd()` - `</form>`
* `textarea($name, $value = '', $moreAttributes = array())` - `<textarea>`
* `button($html = 'Submit', $moreAttributes = array())` - `<button>`
* `select($name, $options, $value = null, $moreAttributes = array())` - `<select><option>`
* `selectByRecordSet($name, $records, $valueKey, $displayKey, $emptyText = '', $value = null, $moreAttributes = array())` - `<select><option>`
