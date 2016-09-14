<?php
class Tester
{
    use TesterFilesUntils;
    use TesterMigrationUntils;
    use TesterStrinUntils;

    // for test that use database
    // pass mysqli databse connection
    // require db/migrate/*.php folder
    public $db_connection = null;

    // when use database, migration files path is required
    public $migrations_path = "db/migrate/*.php";

    // colorize output texts
    // need CLIUntils class available
    // public $colorize_output = true;

    // get test files form paths
    public $tests_paths = [];

    // tests counters
    public $summary_test_counter = 0;
    public $summary_failures_counter = 0;
    public $summary_pending_counter = 0;

    function __construct(Array $params = []) {
        $this->db_connection = $params['db_connection'] ?? $this->db_connection;
        $this->tests_paths = $params['tests_paths'] ?? $this->tests_paths;
    }

    public function runAll()
    {
        if ($this->db_connection) { $this->checkIfTestDatabaseIsUpdated(); }

        $tests_files_paths = $this->getTestFilesFromTestsDirectories();
        foreach ($tests_files_paths as $test_file_path) {
            $this->runTest($test_file_path);
        }
        $this->displaySummary();
    }

    public function runTest($test_file_path)
    {
        include $test_file_path;
        $class_name = $this->fileNameFormPathToClass($test_file_path);
        $test_class = new $class_name();

        $test_methods = $this->getTestMethodsFormClass($test_class);


        $test_counter = $pending_counter = $failures_counter = 0;
        foreach($test_methods as $method_name) {
            $test_start = microtime(true);
            $test_counter++;

            // clear databse before each test
            if ($this->db_connection) { $this->clearDatabaseExceptSchema(); }

            #ob_start();
            try {
            $test_class->$method_name();
              $result = TesterResult::create($test_class, $method_name, 'success');
            }
            catch (Exception $ex) {
              if ($ex instanceof PendingException) {
                $pending_counter++;
                $result = TesterResult::create($test_class, $method_name, 'pending');
              } else {
                $failures_counter++;
                $result = TesterResult::create($test_class, $method_name, 'error', $ex);
              }
            }
            #$output = ob_get_clean();

            #$result->set_output($output);
            $this->log($result);

            $test_stop = microtime(true);
            $execute_time = round(($test_stop-$test_start), 2);
            if ($execute_time > 3) {
              echo CLIUntils::colorize("\n\n Długi czas wywołania: ".get_class($test_class)."->".$method_name." (". $execute_time."sec) \n\n", 'FAILURE');
            }

        }



        $this->summary_test_counter += $test_counter;
        $this->summary_failures_counter += $failures_counter;
        $this->summary_pending_counter += $pending_counter;
    }





#    public function runSingle($class, $method)
#    {
#        // sprawwdzenie czy wszystkie migracje sa wykonane
#        if (!MigrationTools::isAllMigrationsMade()) {
#            die(CLIUntils::colorize("\nMigrate tests database first.\n\n", 'FAILURE'));
#        }

#        // list all filest in spec directory
#        $files_paths = FilesUntils::getListFilesPathFromDirectoryAndSubfolders('spec');

#        // grab only file with the class
#        foreach ($files_paths as $files_path) {
#            $path_parts = pathinfo($files_path);
#            if ($path_parts['basename'] == $class.'.php') {
#                $path = $files_path;
#            }
#        }

#        // counter to display tests count on summary
#        $test_counter = $failures_counter = 0;
#        include $path;
#        $test = new $class();
#        $test->run_test($method);

#        $test_counter += $test->test_counter;
#        $failures_counter += $test->failures_counter;

#        // for display test summary
#        if ($failures_counter != 0) {
#            echo CLIUntils::colorize("\n\n".$test_counter.' examples, '.$failures_counter.' failures', 'FAILURE');
#        } else {
#            echo CLIUntils::colorize("\n\n".$test_counter.' examples, 0 failures', 'SUCCESS');
#        }
#    }

    public function displaySummary()
    {
        // for display test summary
        if ($this->summary_failures_counter != 0) {
            echo CLIUntils::colorize("\n\n".$this->summary_test_counter.' examples, '.$this->summary_failures_counter.' failures ', 'FAILURE');
        } else {
            echo CLIUntils::colorize("\n\n".$this->summary_test_counter.' examples, 0 failures ', 'SUCCESS');
        }
        if ($this->summary_pending_counter != 0) {
            echo CLIUntils::colorize($this->summary_pending_counter.' pending', 'WARNING');
        }
    }


    public function checkIfTestDatabaseIsUpdated()
    {
        // check if test database is update to migration
        if (!$this->isAllMigrationsMade()) {
            die(  CLIUntils::colorize("\nMigrate tests database first.\n\n", 'FAILURE')  );
        }
    }


    public function getTestMethodsFormClass($class)
    {
        # reports information about a class
        $reflection = new ReflectionClass($class);
        $test_methods = [];
        foreach($reflection->GetMethods() as $method) {
            $method_name = $method->getName();
            # if methot start with "test_"
            if (strlen($method_name) > 5 && substr($method_name, 0, 5 ) == 'test_') {
                $test_methods[] = $method_name;
            }
        }
        return $test_methods;
    }


















#
#    protected $test_log = [];
#    public $test_counter = 0;
#    public $failures_counter = 0;
#    public $pending_counter = 0;
#
#
#    public function runTests() {
#
#
#
#        # reports information about a class
#        $class = new ReflectionClass($this);
#        foreach($class->GetMethods() as $method) {
#
#          $methodname = $method->getName();
#
#          # if methot start with "test_"
#          if (strlen( $methodname ) > 5 && substr( $methodname, 0, 5 ) == 'test_') {
#            $test_start = microtime(true);
#
#            $this->test_counter++;
#
#            # wyzerowanie bazy danych przed kazdym testem
#            $this->clearDatabaseExceptSchema();
#
#            ob_start();
#            try {
#            $this->$methodname();
#              $result = TesterResult::create_success($this, $method);
#            }
#            catch (Exception $ex) {
#              if ($ex instanceof PendingException) {
#                $this->pending_counter++;
#                $result = TesterResult::create_pending($this, $method);
#              } else {
#                $this->failures_counter++;
#                $result = TesterResult::create_failure($this, $method, $ex);
#              }
#            }
#            $output = ob_get_clean();
#
#            $result->set_output($output);
#            $this->log($result);
#
#            $test_stop = microtime(true);
#            $execute_time = round(($test_stop-$test_start), 2);
#            if ($execute_time > 3) {
#              echo CLIUntils::colorize("\n\n Długi czas wywołania: ".get_class($this)."->".$methodname." (". $execute_time."sec) \n\n", 'FAILURE');
#            }
#          }
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
        # $this->test_log[] = $result;

        if ($result->status == 'success') {
            echo CLIUntils::colorize('.', 'SUCCESS');
        }

        if ($result->status == 'pending') {
            echo CLIUntils::colorize('P', 'WARNING');
        }


        if ($result->status != 'success') {
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
