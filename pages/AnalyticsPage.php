<?php

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeoutException;

class AnalyticsPage {
    private $driver;
    public function __construct(RemoteWebDriver $driver) {
        $this->driver = $driver;
    }
    public function goTo() {
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
        $this->driver->takeScreenshot('analytics.png');
    }
} 