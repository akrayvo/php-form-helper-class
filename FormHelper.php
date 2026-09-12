<?php

/**
 * Class FormHelper - class to display form elements in HTML
 */

class FormHelper
{
    // -----------------------------------------------------------------------------
    // Configuration
    // -----------------------------------------------------------------------------

    // configuration variables; see setters for details    
    private $doAddIdAttributeFromName = false;
    private $doReturnHtml = false;
    private $isXhtml = false;
    private $doPassedStringCleanup = true;
    private $doSelectOptionValueEqualsText = false;


    /**
     * automatically add an "id" attribute with the same value as "name"?
     * 
     * does not affect radio inputs because they can have 
     * multiple elements with the same "name" attribute
     * 
     * if false, id's can be added with the $moreAttributes parameter
     * 
     * if true, id's can be overridden with the $moreAttributes parameter
     * 
     * default = false
     */
    public function setDoAddIdAttributeFromName($value)
    {
        $this->doAddIdAttributeFromName = $this->returnBoolean($value);
    }

    /**
     * return the html elements as a string?
     * 
     * if true, html is returned
     * echo $form->text('name', $name);
     * $html = $form->text('name', $name); echo $html;
     * 
     * if false, html is directly output
     * $form->text('name', $name);
     * 
     * * default = false
     */
    public function setDoReturnHtml($value)
    {
        $this->doReturnHtml = $this->returnBoolean($value);
    }

    /**
     * output html as XHTML-style syntax?
     * 
     * self-closing elements
     * if true  <input type="text" name="name" />
     * if false <input type="text" name="name">
     * 
     * boolean attributes (selected, readonly, etc) will have values that match the attribute
     * if true  <option value="1" selected="selected"> vs.
     * if false <option value="1" selected>
     * 
     * * default = false
     */
    public function setIsXhtml($value)
    {
        $this->isXhtml = $this->returnBoolean($value);
    }

    /**
     * clean up passed values?
     * 
     * if true, removes HTML tags (strip_tags) and trims whitespace (php trim)
     * 
     * if false, passed variables are unchanged
     * 
     * used in getPost(), getGet(), and getPassed() functions
     * 
     * default = true
     */
    public function setDoPassedStringCleanup($value)
    {
        $this->doPassedStringCleanup = $this->returnBoolean($value);
    }

    /**
     * make the value equal to the display text for options in dropdown menus (html select)?
     * 
     * if true, html select option value and display text will both be set 
     *      to the passed options array item value. so array(2=>"a", 3=>"b") outputs
     *      <option value="a">a</option><option value="b">b</option>
     * 
     * if false, html select option value will be the array item key and the 
     *      html display text will be the array item value. so array(2=>"a", 3=>"b") outputs
     *      <option value="2">a</option><option value="3">b</option>
     * 
     * * default = false
     */
    public function setDoSelectOptionValueEqualsText($value)
    {
        $this->doSelectOptionValueEqualsText = $this->returnBoolean($value);
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
        if (!empty($this->isXhtml)) {
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
            if ($this->isXhtml) {
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
            if ($this->isXhtml) {
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
                    if ($this->doSelectOptionValueEqualsText) {
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
                if ($this->doSelectOptionValueEqualsText) {
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
     * note that an id attribute is not automatically added when $doAddIdAttributeFromName is true.
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
     * checks that the variable exists, so it will not produce a PHP warning
     * 
     * note that $_POST takes precedence over $GET, so if both are passed $_POST will be returned
     */
    public function getPassed($var, $returnOnfail = '')
    {
        if (isset($_POST[$var])) {
            return $this->getPost($var, $returnOnfail);
        } elseif (isset($_GET[$var])) {
            return $this->getGet($var, $returnOnfail);
        }

        return $returnOnfail;
    }

    /**
     * get variables passed by post (form) 
     */
    public function getPost($var, $returnOnfail = '')
    {
        if (isset($_POST[$var])) {
            return $this->stringCleanup($_POST[$var]);
        }

        return $returnOnfail;
    }

    /**
     * get variables passed by get (url parameters) 
     */
    public function getGet($var, $returnOnfail = '')
    {
        if (isset($_GET[$var])) {
            return $this->stringCleanup($_GET[$var]);
        }

        return $returnOnfail;
    }


    // -----------------------------------------------------------------------------
    // Public Helper Functions
    // -----------------------------------------------------------------------------

    /**
     * escape string to display in HTML
     */
    public function htmlEscape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * converts value to boolean
     */
    public function returnBoolean($value)
    {
        if ($value) {
            return true;
        }
        return false;
    }

    /**
     * remove html tags from string. "<b>hello</b>" becomes "hello"
     * trim string ex: " hello " becomes "hello"
     */
    public function stringCleanup($string)
    {
        if (!$this->doPassedStringCleanup || !is_string($string)) {
            return $string;
        }
        $string = trim(strip_tags($string));
        return $string;
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
     * output or return the html based on the doReturnHtml setting
     */
    private function htmlOutputOrReturn($html)
    {
        if ($this->doReturnHtml) {
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
                    if ($this->isXhtml) {
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
     * requirements:
     *      $doAddIdAttributeFromName must be true
     *      "name" attribute must be set
     *      "id" attribute must NOT be set
     *      "type" attribute must NOT be "radio". (radios will
     *          likely have multiple elements with the same name)
     */
    private function checkAddIdAttributeFromName($attributes)
    {
        if (!$this->doAddIdAttributeFromName) {
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
            // do NOT automatically add id's to radios based on
            //      name. multiple radios usually have the same name.
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
            if ($this->isXhtml) {
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
}
