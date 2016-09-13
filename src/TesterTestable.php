<?php
/**
* Provides a base class to derive tests from
*/
abstract class TesterTestable
{
    protected $test_log = [];
    public $test_counter = 0;
    public $failures_counter = 0;
    public $pending_counter = 0;

    public function run_tests() {
        # reports information about a class
      	$class = new ReflectionClass($this);
        foreach($class->GetMethods() as $method) {


          $methodname = $method->getName();

          # if methot start with "test_"
        	if (strlen( $methodname ) > 5 && substr( $methodname, 0, 5 ) == 'test_') {
            $test_start = microtime(true);

            $this->test_counter++;

            # wyzerowanie bazy danych przed kazdym testem
            MyDB::clearDatabaseExceptSchema();

            ob_start();
      			try {
      				$this->$methodname();
      				$result = TesterResult::create_success($this, $method);
      			}
            catch (Exception $ex) {
              if ($ex instanceof PendingException) {
                $this->pending_counter++;
                $result = TesterResult::create_pending($this, $method);
              } else {
                $this->failures_counter++;
                $result = TesterResult::create_failure($this, $method, $ex);
              }
      			}
      			$output = ob_get_clean();

            $result->set_output($output);
      			$this->log($result);

            $test_stop = microtime(true);
            $execute_time = round(($test_stop-$test_start), 2);
            if ($execute_time > 3) {
              echo CLIUntils::colorize("\n\n Długi czas wywołania: ".get_class($this)."->".$methodname." (". $execute_time."sec) \n\n", 'FAILURE');
            }
      		}
      	}
    }


#    public function run_test($method_to_run) {
#        # reports information about a class
#      	$class = new ReflectionClass($this);
#        foreach($class->GetMethods() as $method) {
#            $methodname = $method->getName();

#            if ($method_to_run != $methodname) {
#                continue;
#            }

#            # if methot start with "test_"
#          	if (strlen( $methodname ) > 5 && substr( $methodname, 0, 5 ) == 'test_') {
#                $test_start = microtime(true);

#                $this->test_counter++;

#                # wyzerowanie bazy danych przed kazdym testem
#                MyDB::clearDatabaseExceptSchema();

#                ob_start();
#          			try {
#            				$this->$methodname();
#            				$result = TesterResult::create_success($this, $method);
#          			}
#                #catch(UnexpectedValueException $ex) {
#          			#	$result = TesterResult::create_pending($this, $method);
#          			#}
#          			catch (Exception $ex) {
#                    if ($ex instanceof PendingException) {
#                        $result = TesterResult::create_pending($this, $method);
#                    } else {
#                        $this->failures_counter++;
#                        $result = TesterResult::create_failure($this, $method, $ex);
#                    }
#          			}
#          			$output = ob_get_clean();

#                $result->set_output($output);
#          			$this->log($result);

#                $test_stop = microtime(true);
#                $execute_time = round(($test_stop-$test_start), 2);
#                if ($execute_time > 3) {
#                    echo CLIUntils::colorize("\n\n Długi czas wywołania: ".get_class($this)."->".$methodname." (". $execute_time."sec) \n\n", 'FAILURE');
#                }
#        		}
#        }
#    }

    public function pending() {
      throw new PendingException('pending');
    }

    /**
     * Logs the result of a test. keeps track of results for later inspection, Overridable to log elsewhere.
     **/
    protected function log(TesterResult $result)
    {
      	$this->test_log[] = $result;

        if ($result->get_success() == 'success') {
            echo CLIUntils::colorize('.', 'SUCCESS');
        }

        if ($result->get_status() == 'pending') {
            echo CLIUntils::colorize('P', 'WARNING');
        }


        if ($result->get_success() != 'success') {
            echo CLIUntils::colorize('F', 'FAILURE');
          	printf( "\n\nTest: %s was a failure (lines: %d-%d; file: %s)\n\n"
          		  ,$result->get_name()
        			  ,$result->get_test()->getStartLine()
        			  ,$result->get_test()->getEndLine()
        			  ,str_replace("/Users/booklet/Sites/api.booklet.local", "", $result->get_test()->getFileName())
          	);

            printf( "%s\n\n"
          		  ,$result->get_exception()
          	);
        }
    }
}
