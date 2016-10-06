<?php
/**
 * Provides a loggable entity with information on a test and how it executed
 **/
class TesterResult
{
    public $testable_instance = null;
    public $is_success = false;
    public $status = 'error';
    public $output = '';
    public $test = null; // ReflectionMethod
    public $exception = null; // Exception

    static public function create($object, $test, $status, Exception $exception = null)
    {
        $result = new self();
        $result->testable_instance = $object;
        $result->status = $status;
        $result->test = $test;
        if (isset($exception)) {
            $result->exception = $exception;
        }

        return $result;
    }

    public function get_output()
    {
        return $this->output;
    }

    // set output from ob_get_clean
    public function set_output($value)
    {
        $this->output = $value;
    }

    public function get_test()
    {
        return $this->test;
    }

    // get test method name
    public function get_name()
    {
        return $this->test->getName();
    }
}
