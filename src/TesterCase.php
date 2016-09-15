<?php
class TesterCase
{
    public function pending() {
        throw new TesterPendingException('pending');
    }

    public function request($method, $url, $token, Array $data=[]) {
        return new TesterTestRequest($method, $url, $token, $data);
    }
}
