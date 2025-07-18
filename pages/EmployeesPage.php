<?php

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeoutException;

class EmployeesPage {
    private $driver;
    public function __construct(RemoteWebDriver $driver) {
        $this->driver = $driver;
    }
    public function goTo() {
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
    }
} 