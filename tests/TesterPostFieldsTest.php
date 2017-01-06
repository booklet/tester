<?php
class TesterPostFieldsTest extends TesterCase
{

  public function testBuildPostFields1()
  {
      $post_fields = new TesterPostFields();
      $data = ['key1' => 'val1', 'key2' => 'val2'];

      $post_data = $post_fields->buildPostFields($data);



      Assert::expect($post_data)->to_equal($data);
  }

  public function testBuildPostFields2()
  {
      $post_fields = new TesterPostFields();
      $data = [
          curl_file_create('test.txt')
      ];
      $post_data = $post_fields->buildPostFields($data);

      Assert::expect($post_data[0]->name)->to_equal('test.txt');
  }

    public function testBuildPostFields3()
    {
        $post_fields = new TesterPostFields();
        $data = [
            'file' => curl_file_create('test.txt'),
            'key1' => 'val1',
            'array1' => [
                'key2' => 'val2',
                'key3' => 'val3'
            ]
        ];
        $post_data = $post_fields->buildPostFields($data);

        // expect
        // Array (
        //     [file] => CURLFile Object (
        //             [name] => test.txt
        //             [mime] =>
        //             [postname] =>
        //         )
        //     [key1] => val1
        //     [array1[key2]] => val2
        //     [array1[key3]] => val3
        // )

        Assert::expect($post_data['array1[key2]'])->to_equal('val2');
    }

    public function testBuildPostFields4()
    {
        $post_fields = new TesterPostFields();
        $data = [
            'key1' => 'val1',
            'array1' => [
                'key2' => 'val2',
                'key3' => 'val3',
                'hey4' => [
                    'key5' => 'val5',
                    'key6' => 'val6'
                ]
            ]
        ];
        $post_data = $post_fields->buildPostFields($data);

        // expect
        // Array (
        //     [key1] => val1
        //     [array1[key2]] => val2
        //     [array1[key3]] => val3
        //     [array1[hey4][key5]] => val5
        //     [array1[hey4][key6]] => val6
        // )

        Assert::expect(count($post_data))->to_equal(5);
    }

}
