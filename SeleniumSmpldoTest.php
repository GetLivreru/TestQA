<?php

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/pages/LoginPage.php';
require_once __DIR__ . '/pages/AnalyticsPage.php';
require_once __DIR__ . '/pages/EmployeesPage.php';

class SeleniumSmpldoTest extends TestCase
{
    protected $driver;

    protected function setUp(): void
    {
        $host = 'http://localhost:4444/wd/hub';
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability('chromeOptions', [
            'args' => ['--start-maximized', '--disable-blink-features=AutomationControlled']
        ]);
        $this->driver = RemoteWebDriver::create($host, $capabilities);
        $this->driver->manage()->timeouts()->implicitlyWait(10);
    }

    public function testSmpldoUiFlow()
    {
        $loginPage = new LoginPage($this->driver);
        $loginPage->login('smpldo.com@gmail.com', '22222');

        $analyticsPage = new AnalyticsPage($this->driver);
        $analyticsPage->goTo();

        $employeesPage = new EmployeesPage($this->driver);
        $employeesPage->goTo();

        $loginPage->logout();
        sleep(5);
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            $this->driver->quit();
        }
    }
}