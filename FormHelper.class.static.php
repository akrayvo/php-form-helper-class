<?php

/**
 * Class FormHelper - class to display form elements in HTML
 */

class FormHelper
{
    // Configuration

    private static $settings = array(
        'exitProgramOnFailure' => false,
        'addIdAttributeFromName' => false,
        'selectOptionValueEqualsDisplayText' => false,
        'passedTrim' => true,
        'passedStripTags' => true,
        'passedConvertToStandardCharacters' => false,
        'returnNullIfUnavailable' => false,
        'returnHtml' => false,
        'xhtmlStyleOutput' => false
    );

    public static function updateSetting($setting, $value)
    {
        if (!isset(self::$settings[$setting])) {
            self::exitProgramError("updateSetting function received invalid setting: " . $setting);
            return;
        }

        if ($value) {
            self::$settings[$setting] = true;
        } else {
            self::$settings[$setting] = false;
        }
    }

    public static function updateSettings($settings)
    {
        if (!is_array($settings)) {
            self::exitProgramError("updateSettings was not passed an array");
            return;
        }

        foreach ($settings as $setting => $value) {
            self::updateSetting($setting, $value);
        }
    }


    // Form Container

    public static function formStart($action = '', $method = '', $moreAttributes = array())
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

        $attributes = self::combineAttributes($attributes, $moreAttributes);

        $html = '<form' . self::attributeArrayToString($attributes) . '>';

        return self::htmlOutputOrReturn($html);
    }

    public static function formEnd()
    {
        $html = '</form>';
        return self::htmlOutputOrReturn($html);
    }


    // Generic Form Input

    public static function input($type, $name, $value = '', $moreAttributes = array())
    {
        $attributes = array(
            'type' => $type,
            'name' => $name,
            'value' => $value
        );

        $attributes = self::combineAttributes($attributes, $moreAttributes);

        $closingSlash = '';
        if (!empty(self::$settings['xhtmlStyleOutput'])) {
            $closingSlash = ' /';
        }

        $html = '<input' . self::attributeArrayToString($attributes) . $closingSlash . '>';

        return self::htmlOutputOrReturn($html);
    }


    // Inputs With Specific Types

    public static function text($name, $value = '', $moreAttributes = array())
    {
        return self::input('text', $name, $value, $moreAttributes);
    }

    public static function password($name, $moreAttributes = array())
    {
        return self::input('password', $name, '', $moreAttributes);
    }

    public static function email($name, $value = '', $moreAttributes = array())
    {
        return self::input('email', $name, $value, $moreAttributes);
    }

    public static function tel($name, $value = '', $moreAttributes = array())
    {
        return self::input('tel', $name, $value, $moreAttributes);
    }

    public static function date($name, $value = '', $moreAttributes = array())
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
        return self::input('date', $name, $value, $moreAttributes);
    }

    public static function color($name, $value = '', $moreAttributes = array())
    {
        $value = self::returnValidHex($value);
        return self::input('color', $name, $value, $moreAttributes);
    }

    public static function number($name, $value = '', $moreAttributes = array())
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

        return self::input('number', $name, $value, $moreAttributes);
    }

    public static function range($name, $min, $max, $value = '', $moreAttributes = array())
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

        return self::input('range', $name, $value, $moreAttributes);
    }

    public static function checkbox($name, $isChecked = false, $value = 1, $moreAttributes = array())
    {
        if (!empty($isChecked)) {
            if (self::$settings['xhtmlStyleOutput']) {
                $moreAttributes['checked'] = 'checked';
            } else {
                $moreAttributes[] = 'checked';
            }
        }

        return self::input('checkbox', $name, $value, $moreAttributes);
    }

    public static function radio($name, $value, $selectedValue = null, $moreAttributes = array())
    {
        if ($value !== null && $selectedValue !== null && $value == $selectedValue) {
            if (self::$settings['xhtmlStyleOutput']) {
                $moreAttributes['checked'] = 'checked';
            } else {
                $moreAttributes[] = 'checked';
            }
        }

        return self::input('radio', $name, $value, $moreAttributes);
    }

    public static function hidden($name, $value = '', $moreAttributes = array())
    {
        return self::input('hidden', $name, $value, $moreAttributes);
    }


    // Other Form Elements (non input) (textarea and select)

    public static function textarea($name, $value = '', $moreAttributes = array())
    {
        $attributes = array('name' => $name);

        $attributes = self::combineAttributes($attributes, $moreAttributes);

        $html = '<textarea' . self::attributeArrayToString($attributes) . '>' .
            self::htmlEscape($value) .
            '</textarea>';

        return self::htmlOutputOrReturn($html);
    }

    public static function select($name, $options, $value = null, $moreAttributes = array())
    {
        $attributes = array('name' => $name);

        $attributes = self::combineAttributes($attributes, $moreAttributes);

        $html = '<select' . self::attributeArrayToString($attributes) . '>';

        foreach ($options as $optionValue => $display) {
            if (is_array($display)) {
                $html .= '<optgroup ' .
                    self::attributeArrayToString(array('label' => $optionValue)) .
                    '>';
                foreach ($display as $groupOptionValue => $groupOptionDisplay) {
                    if (self::$settings['selectOptionValueEqualsDisplayText']) {
                        $groupOptionValue = $groupOptionDisplay;
                    }
                    $html .= self::selectOption(
                        $groupOptionDisplay,
                        $groupOptionValue,
                        $value
                    );
                }
                $html .= '</optgroup>';
            } else {
                if (self::$settings['selectOptionValueEqualsDisplayText']) {
                    $optionValue = $display;
                }
                $html .= self::selectOption($display, $optionValue, $value);
            }
        }

        $html .= '</select>';

        return self::htmlOutputOrReturn($html);
    }

    public static function selectByRecordSet(
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

        return self::select($name, $options, $value, $moreAttributes);
    }


    // Form Buttons

    public static function submit($value = '', $name = '',  $moreAttributes = array())
    {
        // set default name, button input data is rarely processed, so
        //      the name can often use the default value
        if (empty($name)) {
            $name = 'submitInputButton';
        }

        if (empty($value)) {
            $value = 'Submit';
        }

        return self::input('submit', $name, $value, $moreAttributes);
    }

    public static function reset($value = '', $name = '',  $moreAttributes = array())
    {
        // set default name, button input data is rarely processed, so
        //      the name can often use the default value
        if (empty($name)) {
            $name = 'submitInputReset';
        }

        if (empty($value)) {
            $value = 'Reset';
        }

        return self::input('reset', $name, $value, $moreAttributes);
    }

    public static function button($html = 'Submit', $moreAttributes = array())
    {
        // note that $html is not escaped. this allows images or other HTML inside of the button
        $html = '<button' . self::attributeArrayToString($moreAttributes) . '>' .
            $html .
            '</button>';

        return self::htmlOutputOrReturn($html);
    }


    // Request Passed Values (used in redisplaying a form with errors or form processing)

    public static function getPassed($var, $flags = array())
    {
        if (empty($var)) {
            self::exitProgramError("no variable name passed to getPassed");
        }

        $processedFlags = self::processPassedFlags($flags);
        return self::getPassedInternal($var, $processedFlags);
    }

    public static function getPost($var, $flags = array())
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
        return self::getPassed($var, $flags);
    }

    public static function getGet($var, $flags = array())
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
        return self::getPassed($var, $flags);
    }


    // Public Helper Functions

    public static function htmlEscape($string)
    {
        if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
            // htmlspecialchars $encoding parameter add in PHP 5.2.3
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        }

        return htmlspecialchars($string, ENT_QUOTES);
    }

    public static function convertToStandardCharacters($str)
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

    public static function stripTags($str)
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

    public static function returnValidHex($hex)
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


    // Private Helper Functions (not available outside of the class)

    private static function htmlOutputOrReturn($html)
    {
        if (self::$settings['returnHtml']) {
            return $html;
        }

        echo $html;
        return '';
    }

    private static function attributeArrayToString($attributes)
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

            $value = self::htmlEscape($value);

            if (is_int($name)) {
                // numeric keys are treated as a non-associative array.
                // so attributes without a value can be specified this way (readonly, disabled) 

                if (!in_array($value, $addedAttributes)) {
                    $addedAttributes[] = $value;
                    if (self::$settings['xhtmlStyleOutput']) {
                        $attributeString .= ' ' . $value . '="' . $value . '"';
                    } else {
                        $attributeString .= ' ' . $value;
                    }
                }
            } else {
                $name = self::htmlEscape($name);
                if (!in_array($name, $addedAttributes)) {
                    $addedAttributes[] = $name;
                    $attributeString .= ' ' . $name . '="' . $value . '"';
                }
            }
        }

        return $attributeString;
    }

    private static function checkAddIdAttributeFromName($attributes)
    {
        if (!self::$settings['addIdAttributeFromName']) {
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

    private static function combineAttributes($mainAttributes, $moreAttributes = array())
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

        if (self::checkAddIdAttributeFromName($attributes)) {
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

    private static function selectOption($display, $value, $selectedValue)
    {
        $attributes = array('value' => $value);

        if ($value !== null && $selectedValue !== null && $value == $selectedValue) {
            if (self::$settings['xhtmlStyleOutput']) {
                $attributes['selected'] = 'selected';
            } else {
                $attributes[] = 'selected';
            }
        }

        $html = '<option' .
            self::attributeArrayToString($attributes) .
            '>' .
            self::htmlEscape($display) .
            '</option>';

        return $html;
    }

    private static function exitProgramError($message = '')
    {
        if (!self::$settings['exitProgramOnFailure']) {
            return;
        }

        // display message
        echo "\n<br><div>ERROR\n";
        if (!empty($message)) {
            echo ": " . self::htmlEscape($message);
        }
        echo "\n</div><br>\n";

        die();
    }

    private static function processPassedFlags($flags)
    {
        if (empty($flags)) {
            return array();
        }

        if (!is_array($flags)) {
            if (!is_string($flags)) {
                self::exitProgramError("flags must be an array or string");
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
                self::exitProgramError("invalid flag found: " . $f);
            }
        }
        $flags = $newFlags;

        if (!empty($flags['post']) && !empty($flags['get'])) {
            self::exitProgramError("incompatible flags passed: post and get");
        }
        if (!empty($flags['post']) && !empty($flags['cookie'])) {
            self::exitProgramError("incompatible flags passed: post and cookie");
        }
        if (!empty($flags['get']) && !empty($flags['cookie'])) {
            self::exitProgramError("incompatible flags passed: get and cookie");
        }
        if (!empty($flags['int']) && !empty($flags['float'])) {
            self::exitProgramError("incompatible flags passed: int and float");
        }
        if (!empty($flags['strip-tags']) && !empty($flags['no-strip-tags'])) {
            self::exitProgramError("incompatible flags passed: strip-tags and no-strip-tags");
        }
        if (!empty($flags['trim']) && !empty($flags['no-trim'])) {
            self::exitProgramError("incompatible flags passed: trim and no-trim");
        }
        if (!empty($flags['convert']) && !empty($flags['no-convert'])) {
            self::exitProgramError("incompatible flags passed: convert and no-convert");
        }

        return $newFlags;
    }

    private static function getReturnOnFail($flags = array())
    {
        if (self::$settings['returnNullIfUnavailable']) {
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

    private static function getPassedInternal($var, $flags = array())
    {
        $returnOnFail = self::getReturnOnFail($flags);

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

        return self::getPassedInternalValue($val, $flags);
    }

    private static function getPassedInternalValue($val, $flags)
    {
        $returnOnFail = self::getReturnOnFail($flags);

        if (!empty($flags['array'])) {
            if (is_array($val)) {
                return self::getPassedInternalArray($val, $flags);
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
        } elseif (empty($flags['convert']) && !self::$settings['passedConvertToStandardCharacters']) {
            // convert flag not passed and passedConvertToStandardCharacters is false - do nothing
        } else {
            $val = self::convertToStandardCharacters($val, $flags);
        }

        if (!empty($flags['no-strip-tags'])) {
            // no-strip-tags flag passed - do nothing
        } elseif (empty($flags['strip-tags']) && !self::$settings['passedStripTags']) {
            // strip-tags flag not passed and passedStripTags is false - do nothing
        } else {
            $val = self::stripTags($val);
        }

        if (!empty($flags['no-trim'])) {
            // no-trim flag passed - do nothing
        } elseif (empty($flags['trim']) && !self::$settings['passedTrim']) {
            // trim flag not passed and passedTrim is false - do nothing
        } else {
            $val = trim($val);
        }

        return $val;
    }

    private static function getPassedInternalArray($array, $flags)
    {
        if (!is_array($array)) {
            return self::getReturnOnFail($flags);
        }

        $flagsNoArray = $flags;
        if (isset($flagsNoArray['array'])) {
            unset($flagsNoArray['array']);
        }

        $return = array();
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $return[$k] = self::getPassedInternalArray($v, $flags);
            } else {
                $return[$k] = self::getPassedInternalValue($v, $flagsNoArray);
            }
        }
        return $return;
    }
}
