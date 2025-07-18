<?php

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class SeleniumSmpldoTest extends \PHPUnit\Framework\TestCase
{
    protected $driver;

    protected function setUp(): void
    {
        $host = 'http://localhost:4444/wd/hub';
        $capabilities = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create($host, $capabilities);
    }

    public function testSmpldoUiFlow()
    {
        $this->driver->get('https://app.smpldo.ru');
        try {
            $kzButton = $this->driver->findElement(WebDriverBy::xpath("//div[contains(@class,'ant-segmented-item-label') and contains(text(),'Казахстан')]"));
            if ($kzButton->getAttribute('aria-selected') !== 'true') {
                $kzButton->click();
            }
        } catch (\Exception $e) {}
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::xpath("//span[text()='Электронная почта']/ancestor::button"))
        );
        $emailButton = $this->driver->findElement(WebDriverBy::xpath("//span[text()='Электронная почта']/ancestor::button"));
        $emailButton->click();
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("login_email"))
        );
        $this->driver->findElement(WebDriverBy::id("login_email"))->sendKeys('smpldo.com@gmail.com');
        $this->driver->findElement(WebDriverBy::xpath("//span[text()='Далее']/ancestor::button"))->click();
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input[aria-label^='OTP Input']"))
        );
        $otp = '22222';
        for ($i = 1; $i <= strlen($otp); $i++) {
            $input = $this->driver->findElement(WebDriverBy::cssSelector("input[aria-label='OTP Input $i']"));
            $input->clear();
            $input->sendKeys($otp[$i-1]);
        }
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('.dashboard'))
        );
        $this->driver->takeScreenshot('dashboard.png');
        $this->driver->findElement(WebDriverBy::cssSelector('a[href*="analytics"]'))->click();
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('a[href*="employees"]'))
        );
        $this->driver->findElement(WebDriverBy::cssSelector('a[href*="employees"]'))->click();
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('.employees-list'))
        );
        $this->driver->takeScreenshot('employees.png');
        $this->driver->findElement(WebDriverBy::cssSelector('.logout-button'))->click();
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#email'))
        );
        $this->driver->takeScreenshot('logout.png');
    }

    protected function tearDown(): void
    {
        $this->driver->quit();
    }
}