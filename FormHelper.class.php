<?php

/**
 * Class FormHelper - class to display form elements in HTML
 */

class FormHelper
{
    // -----------------------------------------------------------------------------
    // Configuration
    // -----------------------------------------------------------------------------

    private $settings = array(
        // end program on settings/configuration error?
        // helpful for development, should be false in production
        'exitProgramOnFailure' => false,

        // automatically add an "id" attribute with the same value as "name"?
        // does not affect radio inputs because they can have 
        //      multiple elements with the same "name" attribute
        // does not affect buttons because the class does not automatically add 
        //      a name "attribute" to buttons
        // if false, id attributes can be added with the $moreAttributes parameter
        // if true, id attributes can be overridden with the $moreAttributes parameter
        // false: <input name="first_name">
        // true:  <input name="first_name" id="first_name">
        'addIdAttributeFromName' => false,

        // in a select (dropdown), use each option's display text as its value
        // if false, the passed options parameter should be an associative
        //      array: $options = array('blue'=>'Blue', 'light_green'=>'Light Green');
        // if true, the passed options parameter can be an indexed (non-associative)
        //      array since the key is ignored: $options = array('Blue', 'Light Green');
        // false:  <option value="blue">Blue</option><option value="light_green">Light Green</option>
        // true:  <option value="Blue">Blue</option><option value="Light Green">Light Green</option>
        'selectOptionValueEqualsDisplayText' => false,

        // trim whitespace from the beginning and end of passed values
        // used in the getPassed() function
        // false: " Joe Smith " is unchanged
        // true: " Joe Smith " is converted to "Joe Smith"
        'passedTrim' => true,

        // remove HTML tags and script/style blocks from a string
        // used in the getPassed() function
        // false: "Joe <b>Smith</b>" is unchanged
        // true: "Joe <b>Smith</b>" is converted to "Joe Smith"
        'passedStripTags' => true,

        // converts non-standard (non-ASCII) characters in passed values
        // used in the getPassed() function
        // replaces characters with equivalents when possible, 
        //  otherwise replaces the character with a dash
        // false: "© Déjà vu" is unchanged
        // true: "© Déjà vu" is converted to "- Deja vu"
        'passedConvertToStandardCharacters' => false,

        // when retrieving a passed value, return NULL when variable is not available (not set or invalid)
        // by default, when a variable is not set, the return value is "" (empty string), 0, or an empty array depending on if a flag is set to return
        //      as an int, float, or array. if returnNullIfUnavailable is set to true, null will be returned instead
        // will also return NULL when a variable doesn't match the settings. for instance, the 'array' flag is set, but the value is not an array
        // false: unset variable returns "" (empty string)
        // true:  unset variable returns NULL
        'returnNullIfUnavailable' => false,

        // return the HTML elements as a string?
        // if true, HTML is returned, echo is required
        // if false, HTML is directly output, echo is NOT required
        // false: $form->text('name', $name);
        // true:  echo $form->text('name', $name);
        // true:  $html = $form->text('name', $name); echo $html;
        'returnHtml' => false,

        // output HTML as XHTML-style syntax?
        // closes self-closing elements and boolean attributes (selected, readonly, etc)
        //      will have values that match the attribute
        // false: <input type="text" name="name" readonly>
        // true:  <input type="text" name="name" readonly="readonly" />
        'xhtmlStyleOutput' => false
    );

    /**
     * set the configuration variables
     * values must be valid parameters in the $settings array
     * example usage: $form->updateSetting('addIdAttributeFromName', true);
     * supports chaining: $form->updateSetting('exitProgramOnFailure', true)->updateSetting('addIdAttributeFromName', true);
     */
    public function updateSetting($setting, $value)
    {
        if (!isset($this->settings[$setting])) {
            $this->exitProgramError("updateSetting function received invalid setting: " . $setting);
            return $this;
        }

        if ($value) {
            $this->settings[$setting] = true;
        } else {
            $this->settings[$setting] = false;
        }
        return $this;
    }

    /**
     * set multiple configuration variables at once
     * is passed an array where are keys are settings and array values are values
     * wrapper function for updateSetting()
     * example usage $form->updateSettings(array('addIdAttributeFromName'=>true, 'passedTrim'=>false));
     */
    public function updateSettings($settings)
    {
        if (!is_array($settings)) {
            $this->exitProgramError("updateSettings was not passed an array");
            return $this;
        }

        foreach ($settings as $setting => $value) {
            $this->updateSetting($setting, $value);
        }

        return $this;
    }
   

    // -----------------------------------------------------------------------------
    // Form Container
    // -----------------------------------------------------------------------------

    /**
     * start form <form>
     */
    public function formStart($action = '', $method = '', $moreAttributes = array())
    {
        $attributes = array();

        if (empty($action)) {
            // default action to the current script
            $action = $_SERVER['SCRIPT_NAME'];
        }
        $attributes['action'] = $action;

        // if the method is "get" or "g" then set the method to get. otherwise default to post.
        if (strtolower($method) === 'get' || strtolower($method) === 'g') {
            $method = 'get';
        } else {
            $method = 'post';
        }
        $attributes['method'] = $method;

        $attributes = $this->combineAttributes($attributes, $moreAttributes);

        $html = '<form' . $this->attributeArrayToString($attributes) . '>';

        return $this->htmlOutputOrReturn($html);
    }

    /**
     * end form </form>
     * 
     * Provided for consistency with formStart(), so the form can be
     * opened and closed using FormHelper methods.
     */
    public function formEnd()
    {
        $html = '</form>';
        return $this->htmlOutputOrReturn($html);
    }

    // -----------------------------------------------------------------------------
    // Generic Form Input
    // -----------------------------------------------------------------------------

    /**
     * input elements <input type="text">, <input type="checkbox">, etc
     * 
     * used by the specific input functions such as text(), hidden(), checkbox(), etc.
     *
     * can also be used directly for input types that do not have a
     * specific function in FormHelper. ex: $form->input('url', 'homepage', $homepage);
     * 
     */
    public function input($type, $name, $value = '', $moreAttributes = array())
    {
        $attributes = array(
            'type' => $type,
            'name' => $name,
            'value' => $value
        );

        $attributes = $this->combineAttributes($attributes, $moreAttributes);

        $closingSlash = '';
        if (!empty($this->settings['xhtmlStyleOutput'])) {
            $closingSlash = ' /';
        }

        $html = '<input' . $this->attributeArrayToString($attributes) . $closingSlash . '>';

        return $this->htmlOutputOrReturn($html);
    }


    // -----------------------------------------------------------------------------
    // Inputs With Specific Types
    // -----------------------------------------------------------------------------

    /**
     * <input type="text">
     */
    public function text($name, $value = '', $moreAttributes = array())
    {
        return $this->input('text', $name, $value, $moreAttributes);
    }

    /**
     * <input type="password">
     * 
     * unlike other functions, password has no $value parameter, for security
     * reasons, this class does not set a value for password input types. this
     * can be worked around by directly calling the input function
     */
    public function password($name, $moreAttributes = array())
    {
        return $this->input('password', $name, '', $moreAttributes);
    }

    /**
     * <input type="email">
     */
    public function email($name, $value = '', $moreAttributes = array())
    {
        return $this->input('email', $name, $value, $moreAttributes);
    }

    /**
     * <input type="tel">
     */
    public function tel($name, $value = '', $moreAttributes = array())
    {
        return $this->input('tel', $name, $value, $moreAttributes);
    }

    /**
     * <input type="date">
     * $value can accept a date in any format that is accepted by PHP's 
     * strtotime() function. ex: "2020-01-15", "2020/01/15", 
     * "2020/01/15 12:30PM", "January 15, 2020", "now", "next Thursday", etc
     */
    public function date($name, $value = '', $moreAttributes = array())
    {
        if (empty($value)) {
            $value = '';
        } else {
            // convert date to "Y-m-d" format
            // 
            $unitTime = strtotime($value);
            if ($unitTime === false) {
                $value = '';
            } else {
                $value = date('Y-m-d', $unitTime);
            }
        }
        return $this->input('date', $name, $value, $moreAttributes);
    }

    /**
     * <input type="color">
     */
    public function color($name, $value = '', $moreAttributes = array())
    {
        $value = $this->returnValidHex($value);
        return $this->input('color', $name, $value, $moreAttributes);
    }

    /**
     * <input type="number">
     */
    public function number($name, $value = '', $moreAttributes = array())
    {
        if (is_string($value)) {
            if (strlen($value) > 0 && is_numeric($value)) {
                // convert valid number string to float
                $value = floatval($value);
            } else {
                // the string is empty or is not a number. set null
                $value = null;
            }
        }

        return $this->input('number', $name, $value, $moreAttributes);
    }

    /**
     * <input type="range">
     */
    public function range($name, $min, $max, $value = '', $moreAttributes = array())
    {
        $moreAttributes['min'] = intval($min);
        $moreAttributes['max'] = intval($max);

        if (is_string($value)) {
            if (strlen($value) > 0 && is_numeric($value)) {
                // convert valid number string to float
                $value = floatval($value);
            } else {
                // the string is empty or is not a number. set null
                $value = null;
            }
        }

        return $this->input('range', $name, $value, $moreAttributes);
    }

    /**
     * <input type="checkbox">
     * 
     * unlike other functions, has the $isChecked parameter before $value. this is because
     * the value is often not important when processing checkboxes. when each checkbox has a 
     * different name, checking whether the name is present in $_POST is enough to determine
     * which checkboxes were selected, so the default value can generally be used.
     */
    public function checkbox($name, $isChecked = false, $value = 1, $moreAttributes = array())
    {
        if (!empty($isChecked)) {
            if ($this->settings['xhtmlStyleOutput']) {
                $moreAttributes['checked'] = 'checked';
            } else {
                $moreAttributes[] = 'checked';
            }
        }

        return $this->input('checkbox', $name, $value, $moreAttributes);
    }

    /**
     * <input type="radio">
     * 
     * if $value is equal to $selectedValue, the radio button will be selected.
     * this is useful when adding radio buttons in a loop because this function
     * takes care of the comparison.
     */
    public function radio($name, $value, $selectedValue = null, $moreAttributes = array())
    {
        if ($value !== null && $selectedValue !== null && $value == $selectedValue) {
            if ($this->settings['xhtmlStyleOutput']) {
                $moreAttributes['checked'] = 'checked';
            } else {
                $moreAttributes[] = 'checked';
            }
        }

        return $this->input('radio', $name, $value, $moreAttributes);
    }

    /**
     * <input type="hidden">
     */
    public function hidden($name, $value = '', $moreAttributes = array())
    {
        return $this->input('hidden', $name, $value, $moreAttributes);
    }


    // -----------------------------------------------------------------------------
    // Other Form Elements (non input) (textarea and select)
    // -----------------------------------------------------------------------------

    /**
     * <textarea>
     */
    public function textarea($name, $value = '', $moreAttributes = array())
    {
        $attributes = array('name' => $name);

        $attributes = $this->combineAttributes($attributes, $moreAttributes);

        $html = '<textarea' . $this->attributeArrayToString($attributes) . '>' .
            $this->htmlEscape($value) .
            '</textarea>';

        return $this->htmlOutputOrReturn($html);
    }

    /**
     * <select>
     * 
     * $options is an array of key/value pairs that will become the html options
     * 
     * $options accepts 2 dimensional arrays. the key of the outer array will
     *      be the label of an optgroup
     * 
     * $options = array('austin'=>'Austin', 'dallas'=>'Dallas', 'seattle'=>'Seattle');
     * 
     * $options = array(
     *      'Texas'=>array('austin'=>'Austin', 'dallas'=>'Dallas'),
     *      'Washington'=>array('seattle'=>'Seattle')
     * );
     */
    public function select($name, $options, $value = null, $moreAttributes = array())
    {
        $attributes = array('name' => $name);

        $attributes = $this->combineAttributes($attributes, $moreAttributes);

        $html = '<select' . $this->attributeArrayToString($attributes) . '>';

        foreach ($options as $optionValue => $display) {
            if (is_array($display)) {
                $html .= '<optgroup ' .
                    $this->attributeArrayToString(array('label' => $optionValue)) .
                    '>';
                foreach ($display as $groupOptionValue => $groupOptionDisplay) {
                    if ($this->settings['selectOptionValueEqualsDisplayText']) {
                        $groupOptionValue = $groupOptionDisplay;
                    }
                    $html .= $this->selectOption(
                        $groupOptionDisplay,
                        $groupOptionValue,
                        $value
                    );
                }
                $html .= '</optgroup>';
            } else {
                if ($this->settings['selectOptionValueEqualsDisplayText']) {
                    $optionValue = $display;
                }
                $html .= $this->selectOption($display, $optionValue, $value);
            }
        }

        $html .= '</select>';

        return $this->htmlOutputOrReturn($html);
    }

    /**
     * <select>
     * 
     * $records is a 2 dimensional array, such as results from a database query
     * 
     * example usage:
     * array(
     *      array('id'=>123, 'name'=>'Bob Jones', 'email'=>'bob@test.com'),
     *      array('id'=>356, 'name'=>'Jim Smith', 'email'=>'jim@test.com')
     * )
     * $valueKey and $displayKey are the array keys in each record for the value and
     * display text for each html option so $valueKey='id', $displayKey='name' will output 
     * <option value="123">Bob Jones</option><option value="356">Jim Smith</option>
     * 
     * $emptyText is the optional first empty option with an empty value in the dropdown
     * menu. used to keep the first option from being selected when the form is loaded 
     * and/or to allow a field to be skipped.
     * $emptyText = 'Select An Item' will add a new first option 
     * <option value="">Select An Item</option>
     * 
     * if $emptyText is empty, no additional option will be added
     */
    public function selectByRecordSet(
        $name,
        $records,
        $valueKey,
        $displayKey,
        $emptyText = '',
        $value = null,
        $moreAttributes = array()
    ) {
        $options = array();

        if (!empty($emptyText)) {
            $options[''] = $emptyText;
        }

        foreach ($records as $record) {
            if (isset($record[$valueKey]) && isset($record[$displayKey])) {
                $options[$record[$valueKey]] = $record[$displayKey];
            }
        }

        return $this->select($name, $options, $value, $moreAttributes);
    }


    // -----------------------------------------------------------------------------
    // Form Buttons
    // -----------------------------------------------------------------------------

    /**
     * <input type="submit">
     * 
     * unlike other functions, $value comes before $name because the button 
     * value (text that displays on the button) is generally more important than its name.
     */
    public function submit($value = '', $name = '',  $moreAttributes = array())
    {
        // set default name, button input data is rarely processed, so
        //      the name can often use the default value
        if (empty($name)) {
            $name = 'submitInputButton';
        }

        if (empty($value)) {
            $value = 'Submit';
        }

        return $this->input('submit', $name, $value, $moreAttributes);
    }

    /**
     * <input type="reset">
     * 
     * unlike other functions, $value comes before $name because the button 
     * value (text that displays on the button) is generally more important than its name.
     */
    public function reset($value = '', $name = '',  $moreAttributes = array())
    {
        // set default name, button input data is rarely processed, so
        //      the name can often use the default value
        if (empty($name)) {
            $name = 'submitInputReset';
        }

        if (empty($value)) {
            $value = 'Reset';
        }

        return $this->input('reset', $name, $value, $moreAttributes);
    }

    /**
     * <button>
     *
     * note that an id attribute is not automatically added when addIdAttributeFromName is true.
     * this is because no $name parameter is passed to derive the id from.
     * if an id attribute is needed, it must be passed in the $moreAttributes array
     */
    public function button($html = 'Submit', $moreAttributes = array())
    {
        // note that $html is not escaped. this allows images or other HTML inside of the button
        $html = '<button' . $this->attributeArrayToString($moreAttributes) . '>' .
            $html .
            '</button>';

        return $this->htmlOutputOrReturn($html);
    }


    // -----------------------------------------------------------------------------
    // Request Passed Values (used in redisplaying a form with errors or form processing)
    // -----------------------------------------------------------------------------

    /**
     * get variables passed by post or get (form or url)
     * 
     * checks that the variable exists, so it will not produce a PHP warning if the variable is not set
     * 
     * note that $_POST takes precedence over $_GET, so if both are passed $_POST will be returned
     * 
     * $flags are a list of flags to determine where the data comes from and how it is processed.
     * 
     * $flags can be passed as an array or a string separated by commas or spaces. ex:
     * $flags = array('post', 'float');  or  $flags = "post float";  or   $flags = "post,float";
     * 
     * valid $flags: post, get, cookie, int, float, array, strip-tags, no-strip-tags, trim, no-trim, convert, no-convert
     * post - retrieve the variable from POST only
     * get - retrieve the variable from GET only
     * cookie - retrieve the variable from COOKIE only
     * int - convert retrieved value to an integer
     * float - convert retrieved value to a float
     * array - process value as an array, can be used with int or float to process an array of integers or floats
     * strip-tags, no-strip-tags - override the "passedStripTags" setting. see setting for details
     * trim, no-trim - override the "passedTrim" setting. see setting for details
     * convert, no-convert - override the "passedConvertToStandardCharacters" setting. see setting for details
     */
    public function getPassed($var, $flags = array())
    {
        if (empty($var)) {
            $this->exitProgramError("no variable name passed to getPassed");
        }

        $processedFlags = $this->processPassedFlags($flags);
        return $this->getPassedInternal($var, $processedFlags);
    }

    /**
     * get variables passed by POST (form)
     * 
     * shorthand for calling getPassed with a "post" flag
     * getPassed("name", "post") is the same as getPost("name")
     */
    public function getPost($var, $flags = array())
    {
        if (is_string($flags)) {
            $flags .= ', post';
        } else {
            if (is_array($flags)) {
                $flags[] = 'post';
            } else {
                $flags = array('post');
            }
        }
        return $this->getPassed($var, $flags);
    }

    /**
     * get variables passed by GET (URL query string parameters) 
     * 
     * shorthand for calling getPassed with a "get" flag
     * getPassed("name", "get") is the same as getGet("name")
     */
    public function getGet($var, $flags = array())
    {
        if (is_string($flags)) {
            $flags .= ', get';
        } else {
            if (is_array($flags)) {
                $flags[] = 'get';
            } else {
                $flags = array('get');
            }
        }
        return $this->getPassed($var, $flags);
    }

    // -----------------------------------------------------------------------------
    // Public Helper Functions
    // -----------------------------------------------------------------------------

    /**
     * escape string to display in HTML
     */
    public function htmlEscape($string)
    {
        if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
            // htmlspecialchars $encoding parameter add in PHP 5.2.3
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        }

        return htmlspecialchars($string, ENT_QUOTES);
    }

    /**
     * replace non-standard (non-ASCII) characters with ASCII equivalents or a dash
     */
    public function convertToStandardCharacters($str)
    {
        // common replacements
        $replace = array(
            'a' => array(chr(195) . chr(161), chr(195) . chr(160), chr(195) . chr(162), chr(195) . chr(164)),
            'A' => array(chr(195) . chr(129), chr(195) . chr(128), chr(195) . chr(130), chr(195) . chr(132)),
            'e' => array(chr(195) . chr(169), chr(195) . chr(168), chr(195) . chr(170), chr(195) . chr(171)),
            'E' => array(chr(195) . chr(137), chr(195) . chr(136), chr(195) . chr(138), chr(195) . chr(139)),
            'i' => array(chr(195) . chr(173), chr(195) . chr(175)),
            'I' => array(chr(195) . chr(141), chr(195) . chr(143)),
            'o' => array(chr(195) . chr(179), chr(195) . chr(182)),
            'O' => array(chr(195) . chr(147), chr(195) . chr(150)),
            'u' => array(chr(195) . chr(186), chr(195) . chr(188)),
            'U' => array(chr(195) . chr(154), chr(195) . chr(156)),
            'n' => array(chr(195) . chr(177)),
            'N' => array(chr(195) . chr(145)),
            'c' => array(chr(195) . chr(167)),
            'C' => array(chr(195) . chr(135)),
            'ss' => array(chr(195) . chr(159)),
            'ae' => array(chr(195) . chr(166)),
            'AE' => array(chr(195) . chr(134)),
            'oe' => array(chr(197) . chr(147)),
            'OE' => array(chr(197) . chr(146)),
            "'" => array(chr(226) . chr(128) . chr(152), chr(226) . chr(128) . chr(153)),
            '"' => array(chr(226) . chr(128) . chr(156), chr(226) . chr(128) . chr(157)),
            '-' => array(chr(226) . chr(128) . chr(147), chr(226) . chr(128) . chr(148)),
            '...' => array(chr(226) . chr(128) . chr(166)),
            '(c)' => array(chr(194) . chr(169)),
            '(TM)' => array(chr(226) . chr(132) . chr(162))
        );

        foreach ($replace as $replacement => $characters) {
            $str = str_replace($characters, $replacement, $str);
        }

        // use PHP's Intl extension to transliterate other characters
        if (function_exists('transliterator_transliterate')) {
            $result = transliterator_transliterate('Any-Latin; Latin-ASCII', $str);
            if ($result !== false) {
                $str = $result;
            }
        }

        // replace remaining non-standard characters with a dash
        $str = preg_replace('/[^\x00-\x7F]/', '-', $str);

        return $str;
    }

    /**
     * remove HTML tags and script/style blocks from a string
     * ex: "<b>hello</b>" becomes "hello"
     */
    public function stripTags($str)
    {
        // remove script and style blocks
        $str = preg_replace(
            array('@<script[^>]*?>.*?</script>@si', '@<style[^>]*?>.*?</style>@si'),
            '',
            $str
        );

        // remove html tags and return
        return strip_tags($str);
    }

    /**
     * validate hex, 3 or 6 digit, with or without #
     * used in the "color" function
     */
    public function returnValidHex($hex)
    {
        if (empty($hex)) {
            return '';
        }

        $hex = strtolower(trim($hex, '#'));

        $length = strlen($hex);
        if ($length != 3 && $length != 6) {
            return '';
        }

        // only valid hex digits
        if (!ctype_xdigit($hex)) {
            return '';
        }

        if ($length == 3) {
            // 3 digit, repeat each. ex 48B becomes 4488BB
            $hex = str_repeat(substr($hex, 0, 1), 2) .
                str_repeat(substr($hex, 1, 1), 2) .
                str_repeat(substr($hex, 2, 1), 2);
        }

        return '#' . $hex;
    }


    // -----------------------------------------------------------------------------
    // Private Helper Functions (not available outside of the class)
    // -----------------------------------------------------------------------------

    /**
     * output or return the html based on the returnHtml setting
     */
    private function htmlOutputOrReturn($html)
    {
        if ($this->settings['returnHtml']) {
            return $html;
        }

        echo $html;
        return '';
    }

    /**
     * converts an array of tag attributes to a string
     * 
     * items with numeric keys will be treated as boolean attributes, so attributes such as "readonly" and "checked" can be added.
     * 
     * ex: array('id'=>'name', 'placeholder'=>'Name', 'readonly') will output 'id="name" placeholder="Name" readonly'
     */
    private function attributeArrayToString($attributes)
    {
        if (empty($attributes) || !is_array($attributes)) {
            return '';
        }

        $addedAttributes = array();
        $attributeString = '';

        foreach ($attributes as $name => $value) {
            if (is_null($value)) {
                continue;
            }

            $value = $this->htmlEscape($value);

            if (is_int($name)) {
                // numeric keys are treated as a non-associative array.
                // so attributes without a value can be specified this way (readonly, disabled) 

                if (!in_array($value, $addedAttributes)) {
                    $addedAttributes[] = $value;
                    if ($this->settings['xhtmlStyleOutput']) {
                        $attributeString .= ' ' . $value . '="' . $value . '"';
                    } else {
                        $attributeString .= ' ' . $value;
                    }
                }
            } else {
                $name = $this->htmlEscape($name);
                if (!in_array($name, $addedAttributes)) {
                    $addedAttributes[] = $name;
                    $attributeString .= ' ' . $name . '="' . $value . '"';
                }
            }
        }

        return $attributeString;
    }

    /**
     * finds if the "id" attribute should be automatically added
     * 
     * requirements:
     * addIdAttributeFromName must be true
     * "name" attribute must be set
     * "id" attribute must NOT be set
     * "type" attribute must NOT be "radio". (radios will likely have multiple elements with the same name)
     */
    private function checkAddIdAttributeFromName($attributes)
    {
        if (!$this->settings['addIdAttributeFromName']) {
            // auto add id setting is off
            return false;
        }

        if (empty($attributes['name'])) {
            // name is not set
            return false;
        }

        if (!empty($attributes['id'])) {
            // id is already set
            return false;
        }

        if (!empty($attributes['type'])) {
            // do NOT automatically add id's to radios based on name. multiple radios usually have the same name.
            if ($attributes['type'] === 'radio') {
                return false;
            }
        }

        return true;
    }

    /**
     * combines the attributes created in this class with ones passed as parameters
     * 
     * adds the id attribute if needed
     */
    private function combineAttributes($mainAttributes, $moreAttributes = array())
    {
        $attributes = array();

        // attributes created in the class are first and can be overwritten
        if (is_array($mainAttributes)) {
            foreach ($mainAttributes as $name => $value) {
                if (is_int($name)) {
                    $attributes[] = $value;
                } else {
                    $name = trim(strtolower($name));
                    $attributes[$name] = $value;
                }
            }
        }

        // attributes passed as parameters are last and can overwrite values
        if (is_array($moreAttributes)) {
            foreach ($moreAttributes as $name => $value) {
                if (is_int($name)) {
                    $attributes[] = $value;
                } else {
                    $name = trim(strtolower($name));
                    $attributes[$name] = $value;
                }
            }
        }

        if ($this->checkAddIdAttributeFromName($attributes)) {
            // add id attribute based on name
            // ex: <input type="text" name="last_name" id="last_name">
            $id = $attributes['name'];
            // handle array variable names, ex "abc[1]" becomes "abc-1" 
            // replace brackets with dashes
            $id = str_replace(array('[', ']'), '-', $id);
            // convert multiple consecutive dashes to single dash
            $id = preg_replace('/\-+/', '-', $id);
            $id = trim($id, '-');
            $attributes['id'] = $id;
        }

        return $attributes;
    }

    /**
     * <option>
     * called in the select() function
     */
    private function selectOption($display, $value, $selectedValue)
    {
        $attributes = array('value' => $value);

        if ($value !== null && $selectedValue !== null && $value == $selectedValue) {
            if ($this->settings['xhtmlStyleOutput']) {
                $attributes['selected'] = 'selected';
            } else {
                $attributes[] = 'selected';
            }
        }

        $html = '<option' .
            $this->attributeArrayToString($attributes) .
            '>' .
            $this->htmlEscape($display) .
            '</option>';

        return $html;
    }

    /**
     * end the program on setup error (invalid settings)
     * only ends the program if exitProgramOnFailure is true
     * completely stops the page from loading, not just the form
     */
    private function exitProgramError($message = '')
    {
        if (!$this->settings['exitProgramOnFailure']) {
            return;
        }

        // display message
        echo "\n<br><div>ERROR\n";
        if (!empty($message)) {
            echo ": " . $this->htmlEscape($message);
        }
        echo "\n</div><br>\n";

        die();
    }

    /**
     * format flags for passed variable processing
     */
    private function processPassedFlags($flags)
    {
        if (empty($flags)) {
            return array();
        }

        if (!is_array($flags)) {
            if (!is_string($flags)) {
                $this->exitProgramError("flags must be an array or string");
                return array();
            }

            // flags is a string
            // each flag is only lower case characters and dashes
            // spaces or commas are treated as separators

            // replace commas with spaces
            $flags = str_replace(',', ' ', $flags);
            // combine spaces
            $flags = preg_replace('/\s+/', ' ', $flags);
            // remove start and end spaces and make lowercase
            $flags = strtolower(trim($flags));

            // convert string to array
            $flags = explode(' ', $flags);
        }

        $newFlags = array();
        // check for invalid flags
        $validFlagAr = array('post', 'get', 'cookie', 'int', 'float', 'array', 'strip-tags', 'no-strip-tags', 'trim', 'no-trim', 'convert', 'no-convert');
        foreach ($flags as $f) {
            if (in_array($f, $validFlagAr)) {
                $newFlags[$f] = true;
            } else {
                $this->exitProgramError("invalid flag found: " . $f);
            }
        }
        $flags = $newFlags;

        if (!empty($flags['post']) && !empty($flags['get'])) {
            $this->exitProgramError("incompatible flags passed: post and get");
        }
        if (!empty($flags['post']) && !empty($flags['cookie'])) {
            $this->exitProgramError("incompatible flags passed: post and cookie");
        }
        if (!empty($flags['get']) && !empty($flags['cookie'])) {
            $this->exitProgramError("incompatible flags passed: get and cookie");
        }
        if (!empty($flags['int']) && !empty($flags['float'])) {
            $this->exitProgramError("incompatible flags passed: int and float");
        }
        if (!empty($flags['strip-tags']) && !empty($flags['no-strip-tags'])) {
            $this->exitProgramError("incompatible flags passed: strip-tags and no-strip-tags");
        }
        if (!empty($flags['trim']) && !empty($flags['no-trim'])) {
            $this->exitProgramError("incompatible flags passed: trim and no-trim");
        }
        if (!empty($flags['convert']) && !empty($flags['no-convert'])) {
            $this->exitProgramError("incompatible flags passed: convert and no-convert");
        }

        return $newFlags;
    }

    /** 
     * get the default return value (value when variable is not set) 
     * 
     * depends on if the returnNullIfUnavailable settings is true and if the int, float, or array flags are set 
     */
    private function getReturnOnFail($flags = array())
    {
        if ($this->settings['returnNullIfUnavailable']) {
            return null;
        }
        if (!empty($flags['array'])) {
            return array();
        }
        if (!empty($flags['int']) || !empty($flags['float'])) {
            return 0;
        }
        return "";
    }

    /**
     * retrieve the passed value from POST, GET, or COOKIE
     * 
     * called by getPassed, then passes the retrieved value to getPassedInternalValue
     */
    private function getPassedInternal($var, $flags = array())
    {
        $returnOnFail = $this->getReturnOnFail($flags);

        if (!empty($flags['post'])) {
            if (!isset($_POST[$var])) {
                return $returnOnFail;
            }
            $val = $_POST[$var];
        } elseif (!empty($flags['get'])) {
            if (!isset($_GET[$var])) {
                return $returnOnFail;
            }
            $val = $_GET[$var];
        } elseif (!empty($flags['cookie'])) {
            if (!isset($_COOKIE[$var])) {
                return $returnOnFail;
            }
            $val = $_COOKIE[$var];
        } elseif (isset($_POST[$var])) {
            $val = $_POST[$var];
        } elseif (isset($_GET[$var])) {
            $val = $_GET[$var];
        } else {
            return $returnOnFail;
        }

        return $this->getPassedInternalValue($val, $flags);
    }

    /**
     * takes the passed value and processes it based on settings
     * 
     * called by getPassedInternal and getPassedInternalArray, returns the final value
     */
    private function getPassedInternalValue($val, $flags)
    {
        $returnOnFail = $this->getReturnOnFail($flags);

        if (!empty($flags['array'])) {
            if (is_array($val)) {
                return $this->getPassedInternalArray($val, $flags);
            }
            return $returnOnFail;
        }

        if (!empty($flags['int'])) {
            if (is_numeric($val)) {
                return intval($val);
            }
            return $returnOnFail;
        }

        if (!empty($flags['float'])) {
            if (is_numeric($val)) {
                return floatval($val);
            }
            return $returnOnFail;
        }

        // process as a string

        if (!is_string($val) && !is_numeric($val) && !is_bool($val)) {
            // type is not easily converted to a string, fail
            return $returnOnFail;
        }

        $val = strval($val);


        if (!empty($flags['no-convert'])) {
            // no-convert flag passed - do nothing
        } elseif (empty($flags['convert']) && !$this->settings['passedConvertToStandardCharacters']) {
            // convert flag not passed and passedConvertToStandardCharacters is false - do nothing
        } else {
            $val = $this->convertToStandardCharacters($val, $flags);
        }

        if (!empty($flags['no-strip-tags'])) {
            // no-strip-tags flag passed - do nothing
        } elseif (empty($flags['strip-tags']) && !$this->settings['passedStripTags']) {
            // strip-tags flag not passed and passedStripTags is false - do nothing
        } else {
            $val = $this->stripTags($val);
        }

        if (!empty($flags['no-trim'])) {
            // no-trim flag passed - do nothing
        } elseif (empty($flags['trim']) && !$this->settings['passedTrim']) {
            // trim flag not passed and passedTrim is false - do nothing
        } else {
            $val = trim($val);
        }

        return $val;
    }

    /**
     * recursively process array elements for passed variables
     * 
     * called by getPassedInternalValue, calls getPassedInternalValue for non-array values
     */
    private function getPassedInternalArray($array, $flags)
    {
        if (!is_array($array)) {
            return $this->getReturnOnFail($flags);
        }

        $flagsNoArray = $flags;
        if (isset($flagsNoArray['array'])) {
            unset($flagsNoArray['array']);
        }

        $return = array();
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $return[$k] = $this->getPassedInternalArray($v, $flags);
            } else {
                $return[$k] = $this->getPassedInternalValue($v, $flagsNoArray);
            }
        }
        return $return;
    }
}
