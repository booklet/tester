<?php
class Assert
{
    use TesterDisplayUntils;

    private static $subject;
    private static $_instance = null;

    // start with setup subject variable
    public static function expect($subject)
    {
        self::$subject = $subject;

        // for Chaining Static Methods
        if (self::$_instance === null) { self::$_instance = new self; }

        return self::$_instance;
    }

    public static function to_equal($val)
    {
        if (self::$subject !== $val) {
            throw new Exception("Subjects are not equal. Expect: \n\n" .
                                 self::display($val) .
                                 "\n\ngot\n\n" .
                                 self::display(self::$subject) .
                                 "\n\n");
      	}
    }

    public static function to_be_not_equal($val)
    {
        if (self::$subject === $val) {
            throw new Exception("Subjects and expect values are equal.\n\n" .
                                 self::display($val) .
                                 "\n\n");
      	}
    }

    public static function to_be_valid()
    {
        if (!self::$subject.isValid()) {
            throw new Exception('Subjects are not valid.');
        }
    }

    public static function to_include_string($text)
    {
        if (!is_string($text)) {
            throw new Exception("Subjects are not text. Expect: \n\n" .
                                 self::display(self::$subject) .
                                 "\n\ngot\n\n" .
                                 self::display($text) .
                                 "\n\n");
        }

        if (strpos(self::$subject, $text) !== false)
        {
          // OK
        } else {
            throw new Exception("Subjects are not contains text. Expect: \n\n" .
                                 self::display(self::$subject) .
                                 "\n\nto include\n\n" .
                                 self::display($text) .
                                 "\n\n");
        }
    }

    public static function to_have_attributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            // attention
            // if key is integer then check only if attrib exist
            if (is_int($key)) {
                if (!property_exists(self::$subject, $value)) {
                    throw new Exception("Subjects does not have the attribute. Expect: \n\n" .
                                         self::display(self::$subject) .
                                         "\n\nto have attribute\n\n" .
                                         self::display($value) .
                                         "\n\n");
                }
            } else {
                if (property_exists(self::$subject, $key) && self::$subject->$key == $value) {
                  // OK
                } else {
                    throw new Exception("Subjects does not have the attribute with value. Expect: \n\n" .
                                         self::display(self::$subject) .
                                         "\n\nto have attribute\n\n" .
                                         self::display($key.' => '.$value) .
                                         "\n\n");
                }
            }
        }
    }

    public static function toBeNull()
    {
        if (self::$subject != null) {
            throw new Exception("Subjects are not null. Expect null got:\n\n" .
                                 self::display(self::$subject) .
                                 "\n\n");
        }
    }

    public static function toNotBeNull()
    {
        if (self::$subject === null) {
            throw new Exception("Subjects is null. Expect any value.\n\n");
        }
    }

    public static function display($variable, $status = 'FAILURE')
    {
        if (class_exists('PP')) {
            $text = PP::print($variable);
        } else {
            $text = print_r($variable, true);
        }

        if (class_exists('CLIUntils')) {
            return CLIUntils::colorizeConsoleOutput($text, $status);
        } else {
            return $text;
        }
    }
}
