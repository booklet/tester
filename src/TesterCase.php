<?php
// WebDriver
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class TesterCase
{
    public $driver = null;

    public function pending()
    {
        throw new TesterPendingException('pending');
    }

    public function request($method, $url, $token, Array $data=[])
    {
        return new TesterTestRequest($method, $url, $token, $data);
    }

    // SELENIUM WEB DRIVER FUNCTIONS
    public function driver() {
        if (!$this->driver) {
            $host = 'http://localhost:4444/wd/hub'; // this is the default
            $capabilities = DesiredCapabilities::chrome();
            $this->driver = RemoteWebDriver::create($host, $capabilities, 5000);
        }
        return $this->driver;
    }
}
