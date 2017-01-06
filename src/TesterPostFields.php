<?php
class TesterPostFields
{
    public function __construct()
    {

    }

    // convert multidimensional array to one level array, see tests for more info
    /**
     * Use this to send data with multidimensional arrays and CURLFiles
     *
     * @param        $data
     * @param string $existing_keys - will set the paramater name, probably don't want to use
     * @param array  $return_array - Can pass data to start with, only put good data here
     *
     * @return array
     * @author Yisrael Dov Lebow <lebow@lebowtech.com>
    */
    public function buildPostFields($data, $existing_keys = '', $return_array = []){
        // jesli to pojedynczy element to zwroc go jako element tablicy
        if (($data instanceof CURLFile) or !(is_array($data) or is_object($data))) {
            $return_array[$existing_keys] = $data;
            return $return_array;
        } else {
            foreach ($data as $key => $item) {
                $data_key = $existing_keys ? $existing_keys."[$key]" : $key;
                $return_array = $this->buildPostFields($item, $data_key, $return_array);
            }
            return $return_array;
        }
    }
}
