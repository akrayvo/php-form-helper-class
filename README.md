# PHP Form Helper Class
[View on GitHub »](https://github.com/akrayvo/php-form-helper-class)

A simple class to display HTML form elements using PHP. 

It takes care of the HTML syntax and encoding when generating form elements. It does **not** handle all HTML (labels, line breaks, etc.), validation, or data processing.

Supports PHP 5.1 through current PHP versions. **PHP 5.1 compatibility is maintained intentionally; this does not indicate that the project is outdated or limited to older PHP versions**.

## Requirements
* PHP >= 5.1

## Installation
Move the **FormHelper.class.php** file to your project.
**include** or **require** the file in your code.

## Basic Example
HTML / PHP code
```
<?php

// include the class file
require_once('../FormHelper.class.php');
// initialize class
$form = new FormHelper();

// get the value passed to the page. check both $_POST and $_GET.
$name = $form->getPassed('name');
$sport = $form->getPassed('sport');
$comments = $form->getPassed('comments');

// hard-coded options for select (dropdown menu) field
// in actual usage, this data could also come from a database or data file
$sports = array(
    '' => '- select a sport -',
    'baseball' => 'Baseball',
    'basketball' => 'basketball',
    'football' => 'Football',
    'soccer' => 'Soccer (European Football)'  
);

// <form>
$form->formStart();

<div><label>Name</label></div>
// <input type="text">
<?php $form->text('name', $name); ?><br><br>

// <input type="text">
<div><label>Favorite Sport</label></div>
// <select>
<?php $form->select('sport', $sports, $sport); ?><br><br>

<div><label>Comments</label></div>
// <textarea>
<?php $form->textarea('comments', $comments); ?><br><br>

<?php 
// <button>
$form->button('Save Info');
?>

<?php 
// </form>
$form->formEnd(); 
?>
```
Generated HTML
```
<form action="/yourPage.php" method="post">
    <div><label>Name</label></div>    
    <input type="text" name="name" value=""><br><br>
    
    <div><label>Favorite Sport</label></div>
    <select name="sport"><option value="">- select a sport -</option><option value="baseball">Baseball</option><option value="basketball">Basketball</option><option value="football">Football</option><option value="soccer">Soccer (European Football)</option></select><br><br>
    
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
<input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" placeholder="Full Name">
```

with class
```
<?php $form->text("full_name", $full_name, array("placeholder"=>"Full Name")); ?>
```

### Dropdown menu (select)

without class
```
<select name="sport">
    <option value="" <?php if ($sport == "") { echo "selected"; } ?>>- select a sport -</option>
    <option value="baseball" <?php if ($sport == "baseball") { echo "selected"; } ?>>Baseball &amp; Softball</option>
    <option value="football" <?php if ($sport == "football") { echo "selected"; } ?>>Football</option>
    <option value="soccer" <?php if ($sport == "soccer") { echo "selected"; } ?>>Soccer (European Football)</option>
</select>
```

with class
```
<?php
$sports = array(
    "- select a sport -" => "", 
    "baseball"=>"Baseball & Softball", 
    "football"=>"Football", 
    "soccer"=>"Soccer (European Football)"
);
$form->select('sport', $sports, $sport);
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

all settings are boolean (true or false) and can be set using the updateSetting function
```
<?php
$form = new FormHelper();
$form->updateSetting('addIdAttributeFromName', true);
$form->updateSetting('passedStripTags', false);
?>
```

### exitProgramOnFailure
* end program on settings/configuration error?
* helpful for development, should be false in production
* default = false


### addIdAttributeFromName
* automatically add an "id" attribute with the same value as "name"?
* does not affect radio inputs because they can have multiple elements with the same "name" attribute
* does not affect buttons because the class does not automatically add a name "attribute" to buttons
* default = false

```
// addIdAttributeFromName = false
// trying to retrieve a passed variable with invalid parameters (both POST and GET)
$form->updateSetting('exitProgramOnFailure', false);
$name = $form->getPassed('first_name', array('post','get'));
// despite error, program will continue with no output error message

// addIdAttributeFromName = true
// trying to retrieve a passed variable with invalid parameters (both POST and GET)
$form->updateSetting('exitProgramOnFailure', true);
$name = $form->getPassed('first_name', array('post','get'));
// will output an error message and end the program
```

### selectOptionValueEqualsDisplayText
* in a select (dropdown), use each option's display text as its value
* if false, the passed options parameter should be an associative array: $options = array('blue'=>'Blue', 'light_green'=>'Light Green');
* if true, the passed options parameter can be an indexed (non-associative) array since the key is ignored: $options = array('Blue', 'Light Green');
* default = false
```
$options = array('NY'=>'New York', 'OH'=>'Ohio');

// selectOptionValueEqualsDisplayText = false
$form->updateSetting('selectOptionValueEqualsDisplayText', false);
$form->select('state', $options);
// <select name="state"><option value="NY">New York</option><option value="OH">Ohio</option></select>

$form->updateSetting('selectOptionValueEqualsDisplayText', true);
$form->select('state', $options);
// <select name="state"><option value="New York">New York</option><option value="Ohio">Ohio</option></select>
```

### passedTrim
* trim whitespace from the beginning and end of passed values
* used in the getPassed() function
* default = true
```
// my_text = "  My Text  " was passed from form

// passedTrim = false | beginning and end whitespace will be retained
$form->updateSetting('passedTrim', false);
var_dump($form->getPassed("my_text"));
// output string(11) "   My Text   "

// passedTrim = true | beginning and end whitespace will be removed
$form->updateSetting('passedTrim', true);
var_dump($form->getPassed("my_text"));
// output string(7) "My Text"
```


### passedStripTags
* remove HTML tags and script/style blocks from a string
* used in the getPassed() function
* default = true
```
// my_text = "<b><i>My Text</i></b>" was passed from form

// passedStripTags = false | HTML tags will be retained
$form->updateSetting('passedStripTags', false);
var_dump($form->getPassed("my_text"));
// output string(21) "<b><i>My Text</i></b>"

// passedStripTags = true | HTML tags will be removed
$form->updateSetting('passedStripTags', true);
var_dump($form->getPassed("my_text"));
// output string(7) "My Text"
```


### passedConvertToStandardCharacters
* converts non-standard (non-ASCII) characters in passed values
* used in the getPassed() function
* replaces characters with equivalents when possible, otherwise replaces the character with a dash
* default = false
```
// my_text = "Déjà Vu" was passed from form

// passedConvertToStandardCharacters = false | non-standard will be retained
$form->updateSetting('passedConvertToStandardCharacters', false);
var_dump($form->getPassed("my_text"));
// output string(9) "Déjà Vu"

// passedConvertToStandardCharacters = true | non-standard will be replaced
$form->updateSetting('passedConvertToStandardCharacters', true);
var_dump($form->getPassed("my_text"));
// output string(7) "Deja Vu"
```


### returnNullIfUnavailable
* when retrieving a passed value, return NULL when variable is not available (not set or invalid)
* used in the getPassed() function
* by default, when a variable is not set, the return value is "" (empty string), 0, or an empty array depending on if a flag is set to return as an int, float, or array. if returnNullIfUnavailable is set to true, null will be returned instead
* will also return NULL when a variable doesn't match the settings. for instance, the 'array' flag is set, but the value is not an array
* default = false
```
// no POST or GET data passed

// returnNullIfUnavailable = false
$form->updateSetting('returnNullIfUnavailable', false);
$value = $form->getPassed('variable_is_not_set');
var_dump($value);
// output: string(0) ""

// returnNullIfUnavailable = true
$form->updateSetting('returnNullIfUnavailable', true);
$value = $form->getPassed('variable_is_not_set');
var_dump($value);
// output: NULL
```


### returnHtml
* return the HTML elements as a string?
* default = false

```
// returnHtml = false | no echo is required to display output
$form->updateSetting('returnHtml', false);
echo $form->text("first_name");
// output: <input type="text" name="first_name" value="">

// returnHtml = true | echo is required to display output
$form->updateSetting('returnHtml', true);
echo $form->text("first_name");
// output: <input type="text" name="first_name" value="">
```

     
### xhtmlStyleOutput
* output XHTML-style HTML
* closes self-closing elements and boolean attributes (selected, readonly, etc) will have values that match the attribute
* default = false

```
// xhtmlStyleOutput = false
$form->updateSetting('xhtmlStyleOutput', false);
$form->text('first_name', '', array('readonly'));
// <input type="text" name="first_name" value="" readonly>

// xhtmlStyleOutput = true
$form->updateSetting('xhtmlStyleOutput', true);
$form->text('first_name', '', array('readonly'));
// <input type="text" name="first_name" value="" readonly="readonly" />
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


## Passing Variables

Processing forms generally requires handling data passed from POST or GET. These functions check that passed data exists, get the value, manipulate it, and return it.

* `getPassed($var, $flags = array())` - get variable passed through POST, GET, or COOKIE. by default will check POST and return the value if set, then check GET and return the value if set. a COOKIE value is only returned when the `cookie` flag is set 
* `getPost($var, $flags = array())` - get variable passed through POST
* `getGet($var, $flags = array())` - get variable passed through GET

# Flags
* `post` - retrieve the variable from POST only
* `get` - retrieve the variable from GET only
* `cookie` - retrieve the variable from COOKIE only. note that COOKIE values are retrievable since they can be processed along with form data. For instance when saving form data to a database or processing an email form, a COOKIE value can be checked to determine if the user is logged in and that info can be processed.
* `int` - convert retrieved value to an integer
* `float` - convert retrieved value to a float
* `array` - process value as an array, can be used with int or float to process an array of integers or floats
* `strip-tags`, `no-strip-tags` - override the "passedStripTags" setting. see setting for details
* `trim`, `no-trim` - override the "passedTrim" setting. see setting for details
* `convert`, `no-convert` - override the "passedConvertToStandardCharacters" setting. see setting for details

# Usage

* flags can be passed as an array or a string separated by commas or spaces. ex: `$flags = array('post', 'float');  or  $flags = "post float";  or   $flags = "post,float";`

## Functions

### Settings
* `updateSetting($setting, $value)` - set configuration variables
### String Manipulation
* `htmlEscape($string)` - escape a string to display in HTML
### Get Passed Data
* `getPassed($var, $flags = array())` - retrieve a value from $_GET, $_POST, or $_COOKIE. default functionality is check $_POST, then check $_GET
* `getPost($var, $flags = array())` - retrieve a value from $_POST
* `getGet($var, $flags = array())` - retrieve a value from $_GET
### input elements
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
* `input($type, $name, $value = '', $moreAttributes = array())` - `<input>` (used for other HTML inputs: url, phone, etc)

### Other form elements
* `formStart($action = '', $method = '', $moreAttributes = array())` - `<form>`
* `formEnd()` - `</form>`
* `textarea($name, $value = '', $moreAttributes = array())` - `<textarea>`
* `button($html = 'Submit', $moreAttributes = array())` - `<button>`
* `select($name, $options, $value = null, $moreAttributes = array())` - `<select><option>`
* `selectByRecordSet($name, $records, $valueKey, $displayKey, $emptyText = '', $value = null, $moreAttributes = array())` - `<select><option>`
