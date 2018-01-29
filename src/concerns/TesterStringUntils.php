<?php
trait TesterStringUntils
{
    // "tests/modules/user/models/users_test.php" => UsersTest
    // "tests/modules/user/models/UsersTest.php" => UsersTest
    public function fileNameFormPathToClass($path)
    {
        $class_name = pathinfo($path)['filename'];

        // Support to load tests who use namespaces
        $class_name = $this->updateClassNameIfUseNamespace($class_name, $path);

        return $this->toCamelCase($class_name);
    }

    // TODO move this, its not string until
    private function updateClassNameIfUseNamespace($class_name, $path)
    {
        if (strpos($path, 'tests/modules/') !== false) {
            $module_name = explode('/', $path)[2];
            $module_name = str_replace('_', '', ucwords($module_name));
            $namespace_class_name = $module_name . '\\' . $class_name;
            if (class_exists($namespace_class_name)) {
                return $namespace_class_name;
            }
        }

        return $class_name;
    }

    // 'one-two-three-four', 'one_two_three_four' => 'OneTwoThreeFour'
    public function toCamelCase($string)
    {
        // dashes
        $string = str_replace('-', ' ', $string);
        // undescore
        $string = str_replace('_', ' ', $string);

        return str_replace(' ', '', ucwords($string));
    }
}
