<?php
class TesterTester
{
    use TesterFilesUntils;
    use TesterMigrationUntils;

    // for test that use database
    // pass mysqli databse connection
    // require db/migrate/*.php folder
    public $db_connection = null;

    //
    public $migrations_path = "db/migrate/*.php";

    // colorize output texts
    // need CLIUntils class available
    // public $colorize_output = true;

    // get test files form paths
    public $tests_paths = [];

    // tests counters
    public $test_counter = 0;
    public $failures_counter = 0;
    public $pending_counter = 0;


    function __construct(Array $params = []) {
        $this->db_connection = $params['db_connection'] ?? $this->db_connection;
        $this->tests_paths = $params['tests_paths'] ?? $this->tests_paths;
    }


    public function runAll()
    {
        // check if test database is update to migration
        if (!$this->isAllMigrationsMade()) {
            die(  CLIUntils::colorize("\nMigrate tests database first.\n\n", 'FAILURE')  );
        }

        // grab only tests files _test.php
        $tests_files_paths = $this->getTestFilesFromTestsDirectories();

        // setup tests counters
        $test_counter = $failures_counter = $pending_counter = 0;

        foreach ($tests_files_paths as $test_file_path) {
            include $test_file_path;
            $class_name = $this->fileNameFormPathToClass($test_file_path);
            $test = new $class_name();
            $test->run_tests();

            $this->test_counter += $test->test_counter;
            $this->failures_counter += $test->failures_counter;
            $this->pending_counter += $test->pending_counter;
        }

        // for display test summary
        if ($this->failures_counter != 0) {
            echo CLIUntils::colorize("\n\n".$this->test_counter.' examples, '.$this->failures_counter.' failures ', 'FAILURE');
        } else {
            echo CLIUntils::colorize("\n\n".$this->test_counter.' examples, 0 failures ', 'SUCCESS');
        }
        if ($this->pending_counter != 0) {
            echo CLIUntils::colorize($this->pending_counter.' pending', 'WARNING');
        }
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



}
