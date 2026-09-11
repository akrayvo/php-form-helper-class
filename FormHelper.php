<?php

/**
 * Class FormHelper - static class to display from elements in HTML
 */

class FormHelper
{
    /**
     * automatically add an "id" attribute with the same value as "name"
     * does not affect radio inputs because they can have 
     *      multiple elements with the same name
     * if set to false, id's can be by added with the $moreAttributes parameter
     * if set to true, id's can be by overridden with the $moreAttributes parameter
     */
    private $doAddIdAttributeFromName = false;

    /**
     * return the html elements as a string
     * if not set, output is written to the screen
     */
    private $doReturnHtml = false;

    /**
     * close tag elements, ex: <input type="input" name="name" />
     *      vs <input type="input" name="name">
     * boolean attributes will have values, ex:
     *      <option value="1" selected="selected"> vs.
     *      <option value="1" selected>
     */
    private $isXhtml = false;

    /**
     * string cleanup of passed variables
     * removes HTML tags (strip_tags)
     * strips whitespace from the beginning and end of a string (trim)
     * used in getPost(), getGet(), and getPassed() functions
     */
    private $doPassedStringCleanup = true;

    /**
     * when an array of data is passed for the options of a dropdown menu (select),
     *      this determines if the value for each option is the array item key or 
     *      the array item value (same as the display)
     * if this is set, select option value and display text will both be set 
     *      to the options array item value. so [2=>'a', => 3=>'b'] will output
     *      <option value="a">a</option><option value="b">b</option>
     * if not set, select option value will be the array key and the display 
     *      text will be the array value. so [2=>'a', => 3=>'b'] will output
     *      <option value="2">a</option><option value="3">b</option>
     */
    private $doSelectOptionValueEqualsText = false;

    /**
     * converts passed value to boolean
     */
    private function returnBoolean($value)
    {
        if ($value) {
            return true;
        }
        return false;
    }

    public function setDoAddIdAttributeFromName($value)
    {
        $this->doAddIdAttributeFromName = $this->returnBoolean($value);
    }

    public function setDoReturnHtml($value)
    {
        $this->doReturnHtml = $this->returnBoolean($value);
    }

    public function setIsXhtml($value)
    {
        $this->isXhtml = $this->returnBoolean($value);
    }

    public function setDoPassedStringCleanup($value)
    {
        $this->doPassedStringCleanup = $this->returnBoolean($value);
    }

    public function setDoSelectOptionValueEqualsText($value)
    {
        $this->doSelectOptionValueEqualsText = $this->returnBoolean($value);
    }

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
     * escape string to display in HTML
     */
    public function htmlEscape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * converts an array of tag attributes to a string
     * numeric keys will be treated as boolen values, so attributes
     *      such as "readonly" and "checked" can be added.
     * ex: ['id'=>'name', 'placeholder'=>'Name', 'readonly'] will ouput
     *      'id="name" placeholder="Name" readonly'
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
                        $attributeString .= ' ' . $value. '="'.$value.'"';
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
            //      name. mulitple radios usually have the same name.
            if ($attributes['type'] === 'radio') {
                return false;
            }
        }

        return true;
    }

    /**
     * combines the attributes created in this class with ones passed as parameters
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
     * get variables passed by post or get (form or url) 
     * checks that the variable exists, so it will not
     *      produce a PHP warning
     * note that $_POST takes precedence over $GET so if
     *      both are passed $_POST will be returned
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
     * checks that the variable exists, so it will not
     *      produce a PHP warning
     */
    public function getPost($var, $returnOnfail = '')
    {
        if (isset($_POST[$var])) {
            return $this->stringCleanup($_POST[$var]);
        }

        return $returnOnfail;
    }

    /**
     * get variables passed by get (url) 
     * checks that the variable exists, so it will not
     *      produce a PHP warning
     */
    public function getGet($var, $returnOnfail = '')
    {
        if (isset($_GET[$var])) {
            return $this->stringCleanup($_GET[$var]);
        }

        return $returnOnfail;
    }

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
        if (strtolower($method) === 'get') {
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
     */
    public function formEnd()
    {
        $html = '</form>';
        return $this->htmlOutputOrReturn($html);
    }

    /**
     * input elements <input type="text">, <input type="checkbox">, etc
     */
    private function input($type, $name, $value = '', $moreAttributes = array())
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

    /**
     * <input type="hidden">
     */
    public function hidden($name, $value = '', $moreAttributes = array())
    {
        return $this->input('hidden', $name, $value, $moreAttributes);
    }

    /**
     * <input type="text">
     */
    public function text($name, $value = '', $moreAttributes = array())
    {
        return $this->input('text', $name, $value, $moreAttributes);
    }

    /**
     * validate hex, 3 or 6 digit, with or without #
     * used in the "color" function
     */
    private function returnValidHex($hex)
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
     *      strtotime() function. ex: "2020-01-15", "2020/01/15", 
     *      "2020/01/15 12:30PM", "January 15, 2020", "now", "next Thursday", etc
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
     * <input type="password">
     * unlike other functions, password has no $value
     */
    public function password($name, $moreAttributes = array())
    {
        return $this->input('password', $name, '', $moreAttributes);
    }

    /**
     * <input type="checkbox">
     * * unlike other functions, has $isChecked parameter before $value
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

        $html = $this->input('checkbox', $name, $value, $moreAttributes);
        return $this->htmlOutputOrReturn($html);
    }

    /**
     * <input type="radio">
     * if $value is equal to $selectedValue, the radio button will be selected. this
     *      way, when radio buttons are added in a loop, this function takes care of
     *      the evalutions
     */
    public function radio($name, $value, $selectedValue = '', $moreAttributes = array())
    {
        if (!empty($value) && !empty($selectedValue) && $value == $selectedValue) {
            if ($this->isXhtml) {
                $moreAttributes['checked'] = 'checked';
            } else {
                $moreAttributes[] = 'checked';
            }
        }

        $html = $this->input('radio', $name, $value, $moreAttributes);
        return $this->htmlOutputOrReturn($html);
    }

    /**
     * <input type="submit">
     * * unlike other functions, the $value parameter is after $name
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

        $moreAttributes = $this->combineAttributes($moreAttributes);

        return $this->input('submit', $name, $value, $moreAttributes);
    }

    /**
     * <input type="reset">
     * * unlike other functions, the $value parameter is after $name
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

        $moreAttributes = $this->combineAttributes($moreAttributes);

        return $this->input('reset', $name, $value, $moreAttributes);
    }

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
     *<button>
     */
    public function button($html = 'Submit', $moreAttributes = array())
    {
        // note that html is not escaped. this will allow images
        $html = '<button' . $this->attributeArrayToString($moreAttributes) . '>' .
            $html .
            '</button>';

        return $this->htmlOutputOrReturn($html);
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

    /**
     * <select>
     * $options is an array of key/value pairs that will become the html options
     * $options accepts 2 dimensional arrays. the key of the inner array will
     *      be the label of an optgroup
     * $options = array('austin'=>'Austin', 'dallas'=>'Dallas', 'seattle'=>'Seattle');
     * $options = array(
     *      'Texas'=>['austin'=>'Austin', 'dallas'=>'Dallas'],
     *      'Washington'=>['seattle'=>'Seattle']);
     */
    public function select($name, $options, $value = null, $moreAttributes = array())
    {
        $attributes = array('name' => $name);

        $attributes = $this->combineAttributes($attributes, $moreAttributes);

        $html = '<select' . $this->attributeArrayToString($attributes) . '>';

        foreach ($options as $optionValue => $display) {
            if (is_array($display)) {
                $html .= '<optgroup ' .
                    $this->attributeArrayToString(['label' => $optionValue]) .
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
     * $records is a 2 dimensional array, such as results from a database query
     * example [ ['id'=>123, 'name'=>'Bob Jones', 'email'=>'bob@test.com'],
     *      ['id'=>356, 'name'=>'Jim Smith', 'email'=>'jim@test.com']]
     * $valueKey and $displayKey are the keys in each record for the value and
     *      display text for each html option
     * $valueKey='id', $displayKey='name' will output 
     *      <option value="123">Bob Jones</option><option value="356">Jim Smith</option>
     * $emptyText is the optional first empty option in the dropdown menu. used to
     *      keep the first option from being selected when the form is loaded and/or to
     *      allow a field to be skipped.
     * $emptyText = 'Select An Item' will add a new first option 
     *      <option value="">Select An Item</option>
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
}