<?php
class AssertTempTest extends TesterCase
{
    public function testFnToEqual()
    {
        Assert::expect('a')->to_equal('a');

        try {
            Assert::expect('a')->to_equal('z');
        } catch (Exception $e) {
            Assert::expect($e->getMessage())->to_include_string('Subjects are not equal');
        }
    }

    public function testFnToBeNotEqual()
    {
        Assert::expect('a')->to_be_not_equal('z');

        try {
            Assert::expect('a')->to_be_not_equal('a');
        } catch (Exception $e) {
            Assert::expect($e->getMessage())->to_include_string('Subjects and expect values are equal');
        }
    }

    public function testFnToIncludeString()
    {
        Assert::expect('lorem ipsum dolor')->to_include_string('ipsum');

        try {
            Assert::expect('lorem ipsum dolor')->to_include_string('xxx');
        } catch (Exception $e) {
            Assert::expect($e->getMessage())->to_include_string('Subjects are not contains text');
        }
    }

    public function testFnToHaveAttributes()
    {
        $test_obj = new stdClass();
        $test_obj->attrib1 = 'value1';
        $test_obj->attrib2 = 'value2';

        Assert::expect($test_obj)->to_have_attributes(['attrib1'=>'value1']);
        Assert::expect($test_obj)->to_have_attributes(['attrib1'=>'value1', 'attrib2'=>'value2']);
        Assert::expect($test_obj)->to_have_attributes(['attrib1', 'attrib2'=>'value2']);

        // good attrib, wrong value
        try {
            Assert::expect($test_obj)->to_have_attributes(['attrib1'=>'wrong_value']);
        } catch (Exception $e) {
            Assert::expect($e->getMessage())->to_include_string('Subjects does not have the attribute with value.');
        }

        // wron attrib, wrong value
        try {
            Assert::expect($test_obj)->to_have_attributes(['wrong_attrib'=>'wrong_value']);
        } catch (Exception $e) {
            Assert::expect($e->getMessage())->to_include_string('Subjects does not have the attribute with value.');
        }
    }

    public function testToBeNull()
    {
        $val = null;

        Assert::expect($val)->toBeNull();

        try {
            $val = 'data';
            Assert::expect($val)->toBeNull();
        } catch (Exception $e) {
            Assert::expect($e->getMessage())->to_include_string('Subjects are not null. Expect null got:');
        }
    }

    public function testToNotBeNull()
    {
        $val = 'data';

        Assert::expect($val)->toNotBeNull();

        try {
            $val = null;
            Assert::expect($val)->toNotBeNull();
        } catch (Exception $e) {
            Assert::expect($e->getMessage())->to_include_string('Subjects is null. Expect any value.');
        }
    }
}
