<?php

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\Exception\NoSuchElementException;

class SeleniumSmpldoTest extends \PHPUnit\Framework\TestCase
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
        $this->performLogin();
        $this->navigateToAnalytics();
        $this->navigateToEmployees();
        $this->performLogout();
        sleep(10); // Ждем 10 секунд перед закрытием браузера
    }

    private function performLogin()
    {
        $this->driver->get('https://app.smpldo.ru');
        try {
            $kzButton = $this->driver->findElement(
                WebDriverBy::xpath("//div[contains(@class,'ant-segmented-item-label') and contains(text(),'Казахстан')]")
            );
            if ($kzButton->getAttribute('aria-selected') !== 'true') {
                $kzButton->click();
                sleep(1);
            }
        } catch (\Exception $e) {}
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::xpath("//span[text()='Электронная почта']/ancestor::button")
            )
        );
        $emailButton = $this->driver->findElement(
            WebDriverBy::xpath("//span[text()='Электронная почта']/ancestor::button")
        );
        $emailButton->click();
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("login_email"))
        );
        $this->driver->findElement(WebDriverBy::id("login_email"))->sendKeys('smpldo.com@gmail.com');
        $this->driver->findElement(
            WebDriverBy::xpath("//span[text()='Далее']/ancestor::button")
        )->click();
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector("input[aria-label^='OTP Input']")
            )
        );
        $otp = '22222';
        for ($i = 1; $i <= strlen($otp); $i++) {
            $input = $this->driver->findElement(
                WebDriverBy::cssSelector("input[aria-label='OTP Input $i']")
            );
            $input->clear();
            $input->sendKeys($otp[$i-1]);
            usleep(200000);
        }
        $this->driver->wait(15)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector('.dashboard, .ant-layout-content, .ant-menu')
            )
        );
        sleep(3);
        $this->driver->takeScreenshot('dashboard.png');
    }

    private function navigateToAnalytics()
    {
        try {
            $analyticsSelectors = [
                "//a[@href='/analytics' and text()='Аналитика']",
                "//a[@href='/analytics']",
                "//span[text()='Аналитика']/ancestor::a",
                "//li[contains(@data-menu-id, 'analytics')]//a",
                "//a[contains(@href, 'analytics')]"
            ];
            $analyticsElement = null;
            foreach ($analyticsSelectors as $selector) {
                try {
                    $analyticsElement = $this->driver->wait(5)->until(
                        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::xpath($selector))
                    );
                    break;
                } catch (TimeoutException $e) {
                    continue;
                }
            }
            if (!$analyticsElement) {
                throw new \Exception("Элемент Analytics не найден ни одним селектором");
            }
            $this->driver->executeScript("arguments[0].scrollIntoView({block: 'center'});", [$analyticsElement]);
            sleep(1);
            try {
                $analyticsElement->click();
            } catch (\Exception $e) {
                $this->driver->executeScript("arguments[0].click();", [$analyticsElement]);
            }
            $this->driver->wait(10)->until(
                WebDriverExpectedCondition::urlContains('analytics')
            );
            sleep(2);
        } catch (\Exception $e) {
            $this->driver->takeScreenshot('analytics_error.png');
            throw $e;
        }
    }

    private function navigateToEmployees()
    {
        try {
            $employeesSelectors = [
                "//div[@class='ant-segmented-item-label' and text()='Сотрудники']",
                "//div[contains(@class, 'ant-segmented-item-label') and contains(text(), 'Сотрудники')]",
                "//span[text()='Сотрудники']",
                "//a[contains(@href, 'employees')]",
                "//div[contains(text(), 'Employees')]",
                "//span[text()='Employees']",
                "//li[contains(@data-menu-id, 'employees')]//a"
            ];
            $employeesElement = null;
            foreach ($employeesSelectors as $selector) {
                try {
                    $employeesElement = $this->driver->wait(5)->until(
                        WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::xpath($selector))
                    );
                    break;
                } catch (TimeoutException $e) {
                    continue;
                }
            }
            if ($employeesElement) {
                $this->driver->executeScript("arguments[0].scrollIntoView({block: 'center'});", [$employeesElement]);
                sleep(1);
                try {
                    $employeesElement->click();
                } catch (\Exception $e) {
                    $this->driver->executeScript("arguments[0].click();", [$employeesElement]);
                }
                sleep(2);
            }
            $this->driver->takeScreenshot('employees.png');
        } catch (\Exception $e) {
            $this->driver->takeScreenshot('employees_error.png');
        }
    }

    private function performLogout()
    {
        try {
            $logoutLink = $this->driver->wait(5)->until(
                WebDriverExpectedCondition::elementToBeClickable(
                    WebDriverBy::xpath("//li[contains(@data-menu-id, '/logout')]//a[@href='/logout']")
                )
            );
            $this->driver->executeScript("arguments[0].scrollIntoView({block: 'center'});", [$logoutLink]);
            $this->driver->executeScript("arguments[0].click();", [$logoutLink]);
            sleep(2);
            $this->driver->takeScreenshot('logout.png');
        } catch (\Exception $e) {
            $this->driver->takeScreenshot('logout_error.png');
        }
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            $this->driver->quit();
        }
    }
}