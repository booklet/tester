<?php
trait TesterStringUntils
{
    // "tests/models/users_test.php" => UsersTest
    // "tests/models/UsersTest.php" => UsersTest
    public function fileNameFormPathToClass($string)
    {
        $file_name = pathinfo($string)['filename'];

        return $this->toCamelCase($file_name);
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
