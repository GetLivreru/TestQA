<?php

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class LoginPage {
    private $driver;
    public function __construct(RemoteWebDriver $driver) {
        $this->driver = $driver;
    }
    public function login(string $email, string $otp) {
        $this->driver->get('https://app.smpldo.ru');
        // Выбор страны (если нужно)
        try {
            $kzButton = $this->driver->findElement(
                WebDriverBy::xpath("//div[contains(@class,'ant-segmented-item-label') and contains(text(),'Казахстан')]")
            );
            if ($kzButton->getAttribute('aria-selected') !== 'true') {
                $kzButton->click();
                sleep(1);
            }
        } catch (\Exception $e) {}
        // Клик по "Электронная почта"
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::xpath("//span[text()='Электронная почта']/ancestor::button")
            )
        );
        $this->driver->findElement(
            WebDriverBy::xpath("//span[text()='Электронная почта']/ancestor::button")
        )->click();
        // Ввод email
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("login_email"))
        );
        $this->driver->findElement(WebDriverBy::id("login_email"))->sendKeys($email);
        $this->driver->findElement(
            WebDriverBy::xpath("//span[text()='Далее']/ancestor::button")
        )->click();
        // Ввод OTP
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector("input[aria-label^='OTP Input']")
            )
        );
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
    public function logout() {
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
} 