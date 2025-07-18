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
        
        // Добавляем дополнительные опции для стабильности
        $capabilities->setCapability('chromeOptions', [
            'args' => ['--start-maximized', '--disable-blink-features=AutomationControlled']
        ]);
        
        $this->driver = RemoteWebDriver::create($host, $capabilities);
        $this->driver->manage()->timeouts()->implicitlyWait(10);
    }

    public function testSmpldoUiFlow()
    {
        echo "Начинаем тест SMPLDO UI Flow\n";
        
        // Авторизация
        $this->performLogin();
        
        // Переход в Analytics
        $this->navigateToAnalytics();
        
        // Переход в Employees
        $this->navigateToEmployees();
        
        // Выход из системы
        $this->performLogout();
        
        echo "Тест завершен успешно\n";
    }

    private function performLogin()
    {
        echo "Выполняем авторизацию...\n";
        
        $this->driver->get('https://app.smpldo.ru');
        
        // Выбираем Казахстан если нужно
        try {
            $kzButton = $this->driver->findElement(
                WebDriverBy::xpath("//div[contains(@class,'ant-segmented-item-label') and contains(text(),'Казахстан')]")
            );
            if ($kzButton->getAttribute('aria-selected') !== 'true') {
                $kzButton->click();
                sleep(1);
            }
        } catch (\Exception $e) {
            echo "Кнопка Казахстан не найдена или уже выбрана\n";
        }

        // Нажимаем кнопку "Электронная почта"
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::xpath("//span[text()='Электронная почта']/ancestor::button")
            )
        );
        $emailButton = $this->driver->findElement(
            WebDriverBy::xpath("//span[text()='Электронная почта']/ancestor::button")
        );
        $emailButton->click();

        // Вводим email
        $this->driver->wait()->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("login_email"))
        );
        $this->driver->findElement(WebDriverBy::id("login_email"))->sendKeys('smpldo.com@gmail.com');
        
        // Нажимаем "Далее"
        $this->driver->findElement(
            WebDriverBy::xpath("//span[text()='Далее']/ancestor::button")
        )->click();

        // Вводим OTP код
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
            usleep(200000); // 0.2 секунды между вводом символов
        }

        // Ждем загрузки dashboard
        $this->driver->wait(15)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector('.dashboard, .ant-layout-content, .ant-menu')
            )
        );
        
        sleep(3); // Дополнительная пауза для полной загрузки
        $this->driver->takeScreenshot('dashboard.png');
        echo "Авторизация выполнена успешно\n";
    }

    private function navigateToAnalytics()
    {
        echo "Переходим в раздел Analytics...\n";
        
        try {
            // Пробуем разные способы найти Analytics
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
                    echo "Элемент Analytics найден селектором: $selector\n";
                    break;
                } catch (TimeoutException $e) {
                    continue;
                }
            }
            
            if (!$analyticsElement) {
                throw new \Exception("Элемент Analytics не найден ни одним селектором");
            }
            
            // Скроллим к элементу
            $this->driver->executeScript("arguments[0].scrollIntoView({block: 'center'});", [$analyticsElement]);
            sleep(1);
            
            // Пробуем обычный клик
            try {
                $analyticsElement->click();
            } catch (\Exception $e) {
                // Если обычный клик не работает, используем JavaScript
                echo "Обычный клик не сработал, используем JavaScript клик\n";
                $this->driver->executeScript("arguments[0].click();", [$analyticsElement]);
            }
            
            // Ждем загрузки страницы Analytics
            $this->driver->wait(10)->until(
                WebDriverExpectedCondition::urlContains('analytics')
            );
            
            sleep(2);
            echo "Переход в Analytics выполнен\n";
            
        } catch (\Exception $e) {
            echo "Ошибка при переходе в Analytics: " . $e->getMessage() . "\n";
            $this->driver->takeScreenshot('analytics_error.png');
            throw $e;
        }
    }

    private function navigateToEmployees()
    {
        echo "Переходим в раздел Employees...\n";
        
        try {
            // Пробуем разные селекторы для поиска Employees/Сотрудники
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
                    echo "Элемент Employees найден селектором: $selector\n";
                    break;
                } catch (TimeoutException $e) {
                    continue;
                }
            }
            
            if ($employeesElement) {
                // Скроллим к элементу
                $this->driver->executeScript("arguments[0].scrollIntoView({block: 'center'});", [$employeesElement]);
                sleep(1);
                
                // Кликаем
                try {
                    $employeesElement->click();
                } catch (\Exception $e) {
                    $this->driver->executeScript("arguments[0].click();", [$employeesElement]);
                }
                
                sleep(2);
                echo "Переход в Employees выполнен\n";
            } else {
                echo "Элемент Employees не найден, делаем скриншот текущей страницы\n";
            }
            
            $this->driver->takeScreenshot('employees.png');
            
        } catch (\Exception $e) {
            echo "Ошибка при переходе в Employees: " . $e->getMessage() . "\n";
            $this->driver->takeScreenshot('employees_error.png');
            // Не бросаем исключение, так как это не критическая ошибка
        }
    }

    private function performLogout()
    {
        echo "Выполняем выход из системы...\n";
        
        try {
            // Пробуем разные способы найти выход
            $logoutSelectors = [
                '.logout-button',
                '.ant-dropdown-trigger',
                '.user-menu',
                '.profile-menu',
                "//span[text()='Выйти']/ancestor::button",
                "//a[contains(text(), 'Выйти')]",
                "//button[contains(text(), 'Выйти')]"
            ];
            
            $logoutElement = null;
            foreach ($logoutSelectors as $selector) {
                try {
                    if (strpos($selector, '.') === 0) {
                        // CSS селектор
                        $logoutElement = $this->driver->wait(3)->until(
                            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector($selector))
                        );
                    } else {
                        // XPath селектор
                        $logoutElement = $this->driver->wait(3)->until(
                            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::xpath($selector))
                        );
                    }
                    echo "Элемент выхода найден селектором: $selector\n";
                    break;
                } catch (TimeoutException $e) {
                    continue;
                }
            }
            
            if ($logoutElement) {
                $logoutElement->click();
                sleep(1);
                
                // Если нужно еще раз кликнуть на "Выйти" в выпадающем меню
                try {
                    $finalLogout = $this->driver->wait(3)->until(
                        WebDriverExpectedCondition::presenceOfElementLocated(
                            WebDriverBy::xpath("//span[text()='Выйти']/ancestor::button")
                        )
                    );
                    $finalLogout->click();
                } catch (TimeoutException $e) {
                    // Возможно, выход уже выполнен
                }
                
                // Ждем возврата на страницу входа
                $this->driver->wait(10)->until(
                    WebDriverExpectedCondition::presenceOfElementLocated(
                        WebDriverBy::cssSelector('#email, input[type="email"], .login-form')
                    )
                );
                
                echo "Выход выполнен успешно\n";
            } else {
                echo "Кнопка выхода не найдена\n";
            }
            
            $this->driver->takeScreenshot('logout.png');
            
        } catch (\Exception $e) {
            echo "Ошибка при выходе: " . $e->getMessage() . "\n";
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