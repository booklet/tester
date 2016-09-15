<?php
class Tester
{
    use TesterFilesUntils;
    use TesterMigrationUntils;
    use TesterStrinUntils;
    use TesterLoger;

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

    // set single test to run
    // "ClassNamseTest:test_to_run"
    public $single_test_to_run = null;

    // tests counters
    public $summary_test_counter = 0;
    public $summary_failures_counter = 0;
    public $summary_pending_counter = 0;

    function __construct(Array $params = []) {
        $this->db_connection = $params['db_connection'] ?? $this->db_connection;
        $this->tests_paths = $params['tests_paths'] ?? $this->tests_paths;
        $this->single_test_to_run = $params['single_test_to_run'] ?? $this->single_test_to_run;
    }

    public function run()
    {
        if ($this->db_connection) { $this->checkIfTestDatabaseIsUpdated(); }

        $tests_files_paths = $this->getTestFilesFromTestsDirectories();
        foreach ($tests_files_paths as $test_file_path) {
            $this->runTest($test_file_path);
        }

        $this->displaySummary();
    }

    private function runTest($test_file_path)
    {
        include $test_file_path;
        $class_name = $this->fileNameFormPathToClass($test_file_path);
        $test_class_instance = new $class_name();

        $test_methods = $this->getTestMethodsFormClass($test_class_instance);

        // setup counter
        $test_counter = $pending_counter = $failures_counter = 0;

        foreach($test_methods as $method_name => $method_obj) {
            $test_start = microtime(true);
            $test_counter++;

            // clear database before each test
            if ($this->db_connection) { $this->clearDatabaseExceptSchema(); }

            try {
            $test_class_instance->$method_name();
                $result = TesterResult::create($test_class_instance, $method_obj, 'success');
            }
            catch (Exception $ex) {
                if ($ex instanceof TesterPendingException) {
                    $pending_counter++;
                    $result = TesterResult::create($test_class_instance, $method_obj, 'pending');
                } else {
                    $failures_counter++;
                    $result = TesterResult::create($test_class_instance, $method_obj, 'error', $ex);
                }
            }

            $this->log($result);

            $test_stop = microtime(true);
            // if test take too long(>3 sec) show info
            $execute_time = round(($test_stop-$test_start), 2);
            if ($execute_time > 3) {
                echo CLIUntils::colorize("\n\n Test take long time (over 3s): ".get_class($test_class_instance)."->".$method_name." (". $execute_time."sec) \n\n", 'FAILURE');
            }
        }

        $this->summary_test_counter += $test_counter;
        $this->summary_failures_counter += $failures_counter;
        $this->summary_pending_counter += $pending_counter;
    }

    private function displaySummary()
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

    private function getTestMethodsFormClass($class)
    {
        # reports information about a class
        $reflection = new ReflectionClass($class);
        $test_methods = [];
        foreach($reflection->GetMethods() as $method_obj) {
            $method_name = $method_obj->getName();
            # if method start with "test_"
            if (strlen($method_name) > 4 && substr($method_name, 0, 4 ) == 'test') {
                // filter if pass single test params
                if ($this->single_test_to_run) {
                    list($class_name, $test_method_name) = explode(':', $this->single_test_to_run);
                    if ($class_name == get_class($class) && $test_method_name == $method_name) {
                        $test_methods[$method_name] = $method_obj;
                    }
                } else {
                    $test_methods[$method_name] = $method_obj;
                }
            }
        }
        return $test_methods;
    }
}
