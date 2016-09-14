<?php
class AssertTempTest
{
    public function test_fn_to_equal()
    {
        Assert::expect('a')->to_equal('a');

        try {
            Assert::expect('a')->to_equal('z');
        } catch (Exception $e) {
            Assert::expect($e->getMessage())->to_include_string('Subjects are not equal');
        }
    }

    public function test_fn_to_be_not_equal()
    {
        Assert::expect('a')->to_be_not_equal('z');

        try {
            Assert::expect('a')->to_be_not_equal('a');
        } catch (Exception $e) {
            Assert::expect($e->getMessage())->to_include_string('Subjects and expect values are equal');
        }
    }

    public function test_fn_to_include_string()
    {
        Assert::expect('lorem ipsum dolor')->to_include_string('ipsum');

        try {
            Assert::expect('lorem ipsum dolor')->to_include_string('xxx');
        } catch (Exception $e) {
            Assert::expect($e->getMessage())->to_include_string('Subjects are not contains text');
        }
    }

    public function test_fn_to_have_attributes()
    {
        $test_obj = new stdClass();
        $test_obj->attrib1 = 'value1';
        $test_obj->attrib2 = 'value2';

        Assert::expect($test_obj)->to_have_attributes(['attrib1'=>'value1']);
        Assert::expect($test_obj)->to_have_attributes(['attrib1'=>'value1', 'attrib2'=>'value2']);
        Assert::expect($test_obj)->to_have_attributes(['attrib1', 'attrib2'=>'value2']);

        # good attrib, wrong value
        try {
            Assert::expect($test_obj)->to_have_attributes(['attrib1'=>'wrong_value']);
        } catch (Exception $e) {
            Assert::expect($e->getMessage())->to_include_string('Subjects does not have the attribute with value.');
        }

        # wron attrib, wrong value
        try {
            Assert::expect($test_obj)->to_have_attributes(['wrong_attrib'=>'wrong_value']);
        } catch (Exception $e) {
            Assert::expect($e->getMessage())->to_include_string('Subjects does not have the attribute with value.');
        }
    }
}
