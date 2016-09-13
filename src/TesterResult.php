<?php
/**
 * Provides a loggable entity with information on a test and how it executed
 **/
class TesterResult
{
    protected $_testable_instance = null;
    protected $_is_success = false;
    protected $_status = 'error';
    protected $_output = '';
    protected $_test = null; # ReflectionMethod
    protected $_exception = null; # Exception

    static public function create_success(Testable $object, ReflectionMethod $test)
    {
        $result = new self();
        $result->testable_instance = $object;
        $result->_is_success = true;
        $result->_status = 'success';
        $result->_test = $test;
        return $result;
    }

    static public function create_pending(Testable $object, ReflectionMethod $test)
    {
        $result = new self();
        $result->testable_instance = $object;
        $result->_is_success = true;
        $result->_status = 'pending';
        $result->_test = $test;
        return $result;
    }

    static public function create_failure(Testable $object, ReflectionMethod $test, Exception $exception)
    {
        $result = new self();
        $result->testable_instance = $object;
        $result->_is_success = false;
        $result->_status = 'error';
        $result->_test = $test;
        $result->_exception = $exception;
        return $result;
    }

    public function get_success()
    {
    	  return $this->_is_success;
    }

    public function get_status()
    {
    	  return $this->_status;
    }

    public function get_output()
    {
    	  return $_output;
    }

    # set output from ob_get_clean
    public function set_output($value)
    {
    	  $_output = $value;
    }

    public function get_test()
    {
    	  return $this->_test;
    }

    # get test method name
    public function get_name()
    {
    	  return $this->_test->getName();
    }


    /**
    * A test class
    *
    * @param  foo bar
    * @return baz
    */
    # get this describle
    public function get_comment()
    {
    	  return $this->parse_comment( $this->_test->getDocComment() );
    }

    private function parse_comment($comment)
    {
      	$lines = explode("\n", $comment);
      	for($i = 0; $i < count( $lines); $i ++ ) {
      		  $lines[$i] = trim($lines[ $i ]);
      	}
      	return implode("\n", $lines);
    }

    public function get_exception()
    {
    	  return $this->_exception;
    }
}
