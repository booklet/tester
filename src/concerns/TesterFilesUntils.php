<?php
trait TesterFilesUntils
{
    public function getListFilesPathFromDirectoryAndSubfolders($dir)
    {
        $di = new RecursiveDirectoryIterator($dir,RecursiveDirectoryIterator::SKIP_DOTS);
        $files = [];
        foreach (new RecursiveIteratorIterator($di) as $fileName => $fileInfo) {
            $path = (string) $fileInfo;
            $files[] = $path;
        }
        return $files;
    }

    // filter array of files paths to grab only test files
    public function getTestsFiles(Array $files_paths)
    {
        $files = [];
        foreach ($files_paths as $file_path) {
            if (substr($file_path, -8) == 'Test.php') {
                $files[] = $file_path;
            }
        }
        return $files;
    }

    // db/migrate/201607061958_CreateUsersTable.php => 201607061958
    public function getVersionFromFilename($file_name)
    {
        preg_match("/\d{12}/", $file_name, $output_array);
        return $output_array[0];
    }

    // get files to tests
    public function getTestFilesFromTestsDirectories()
    {
        // list all files in tests directories
        $files_paths = [];
        foreach ($this->tests_paths as $path) {
          $files_paths = array_merge($files_paths, $this->getListFilesPathFromDirectoryAndSubfolders($path));
        }

        // grab only tests files _test.php
        $tests_files_paths = $this->getTestsFiles($files_paths);

        return $tests_files_paths;
    }
}
