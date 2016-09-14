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
    public $test = null; # ReflectionMethod
    public $exception = null; # Exception


    static public function create_success($object, $test)
    {
        $result = new self();
        $result->testable_instance = $object;

        $result->status = 'success';
        $result->test = $test;
        return $result;
    }

    static public function create_pending($object, $test)
    {
        $result = new self();
        $result->testable_instance = $object;

        $result->status = 'pending';
        $result->test = $test;
        return $result;
    }

    static public function create_failure($object, $test, Exception $exception)
    {
        $result = new self();
        $result->testable_instance = $object;

        $result->status = 'error';
        $result->test = $test;
        $result->exception = $exception;
        return $result;
    }


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






#    public function get_success()
#    {
#    	  return $this->is_success;
#    }

#    public function getstatus()
#    {
#    	  return $this->status;
#    }

#    public function getoutput()
#    {
#    	  return $output;
#    }

#    # set output from ob_get_clean
#    public function setoutput($value)
#    {
#    	  $output = $value;
#    }

    # set output from ob_get_clean
    public function set_output($value)
    {
        $this->output = $value;
    }

    # get test method name
    public function get_name()
    {
    	  return $this->test;
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
    	  return $this->parse_comment($this->test->getDocComment());
    }

    private function parse_comment($comment)
    {
      	$lines = explode("\n", $comment);
      	for($i = 0; $i < count( $lines); $i ++ ) {
      		  $lines[$i] = trim($lines[ $i ]);
      	}
      	return implode("\n", $lines);
    }

    public function getexception()
    {
    	  return $this->exception;
    }
}
