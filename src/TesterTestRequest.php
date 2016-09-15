<?php
class TesterTestRequest
{
    # test request add 'TesterTestRequestBKT: true' to header
    # then application use test database
    public $body;
    public $http_code;

    public function __construct($method, $url, $token, Array $data=[])
    {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if (!empty($data))
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;

            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if (!empty($data)) {
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                }
                break;

            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;

            default: # GET
                if (!empty($data))
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        #curl_setopt($curl, CURLOPT_HEADER, 1); # headers code

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: $token",
            'TesterTestRequestBKT: true'
        ]);

        $this->body = curl_exec($curl);
        $this->http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
    }
}
