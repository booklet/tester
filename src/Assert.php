<?php
class Assert
{
    use TesterDisplayUntils;

    private static $subject;
    private static $_instance = null;

    // start with setup subject variable
    public static function expect($subject) {
        self::$subject = $subject;

        // for Chaining Static Methods
        if (self::$_instance === null) { self::$_instance = new self; }
        return self::$_instance;
    }

    /**
    * Equals
    */
    public static function to_equal($val)
    {
        if (self::$subject !== $val) {
            throw new Exception("Subjects are not equal. Expect: \n\n" .
                                 self::display(print_r($val, true), 'FAILURE') .
                                 "\n\ngot\n\n".
                                 self::display(print_r(self::$subject, true), 'FAILURE') .
                                 "\n\n");
      	}
    }

    /**
    * Not equals
    */
    public static function to_be_not_equal($val)
    {
        if (self::$subject === $val) {
            throw new Exception("Subjects and expect values are equal.\n\n" .
                                 self::display(print_r($val, true), 'FAILURE') .
                                 "\n\n");
      	}
    }

    /**
    * Is valid?
    */
    public static function to_be_valid()
    {
        if (!self::$subject.isValid()) {
           throw new Exception('Subjects are not valid.');
        }
    }

    /**
    * Include string
    */
    public static function to_include_string($text)
    {
        if (!is_string($text)) {
            throw new Exception("Subjects are not text. Expect: \n\n" .
                                 self::display(print_r(self::$subject, true), 'FAILURE') .
                                 "\n\ngot\n\n".
                                 self::display(print_r($text, true), 'FAILURE') .
                                 "\n\n");
        }

        if (strpos(self::$subject, $text) !== false)
        {
          // OK
        } else {
            throw new Exception("Subjects are not contains text. Expect: \n\n" .
                                 self::display(print_r(self::$subject, true), 'FAILURE') .
                                 "\n\nto include\n\n".
                                 self::display(print_r($text, true), 'FAILURE') .
                                 "\n\n");
        }
    }

    /**
    * Have attribute
    */
    public static function to_have_attributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            // attention
            // if key is integer then check only if attrib exist
            if (is_int($key)) {
                if (!property_exists(self::$subject, $value)) {
                    throw new Exception("Subjects does not have the attribute. Expect: \n\n" .
                                         self::display(print_r(self::$subject, true), 'FAILURE') .
                                         "\n\nto have attribute\n\n".
                                         self::display(print_r($value , true), 'FAILURE') .
                                         "\n\n");
                }
            } else {
                if (property_exists(self::$subject, $key) && self::$subject->$key == $value) {
                  // OK
                } else {
                    throw new Exception("Subjects does not have the attribute with value. Expect: \n\n" .
                                         self::display(print_r(self::$subject, true), 'FAILURE') .
                                         "\n\nto have attribute\n\n".
                                         self::display(print_r($key.' => '.$value , true), 'FAILURE') .
                                         "\n\n");
                }
            }
        }
    }


    public static function display($text, $status)
    {
        if (class_exists('CLIUntils')) {
            return CLIUntils::colorize($text, $status);
        } else {
            return $text;
        }
    }

}
