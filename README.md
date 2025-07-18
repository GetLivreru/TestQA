# TestQA

## Описание

Этот проект содержит примеры автоматизации тестирования на PHP с использованием Selenium WebDriver и PHPUnit. Включает:
- UI-тесты для сайта https://app.smpldo.ru
- Пример расчёта расстояния по формуле Haversine (main.php)
- Настроенный CI через GitHub Actions

## Требования
- PHP 8.2+
- Composer
- Расширения PHP: mbstring, curl, dom, json, libxml, tokenizer, xml, xmlwriter
- Google Chrome
- ChromeDriver (лежит в папке `drivers/`)
- Selenium Server (запущен на http://localhost:4444/wd/hub)

## Установка
1. Клонируйте репозиторий:
   ```sh
   git clone <repo-url>
   cd TestQA
   ```
2. Установите зависимости:
   ```sh
   composer install
   ```
3. Убедитесь, что все необходимые расширения PHP включены (см. выше).
4. Запустите Selenium Server и ChromeDriver.

## Запуск тестов
- Запустить все тесты:
  ```sh
  vendor\bin\phpunit
  ```
- Запустить конкретный тест:
  ```sh
  vendor\bin\phpunit SeleniumSmpldoTest.php
  ```

## Структура проекта
- `main.php` — функция расчёта расстояния по Haversine
- `SeleniumSmpldoTest.php` — основной UI-тест (использует Page Object)
- `pages/` — Page Object классы для тестов
- `test.php` — другие тесты
- `drivers/` — ChromeDriver
- `.github/workflows/ci.yml` — CI для GitHub Actions

## CI/CD
При каждом пуше или pull request в ветку `main` автоматически:
- Устанавливаются зависимости
- Запускаются тесты PHPUnit

## Полезные команды
- Проверить расширения PHP:
  ```sh
  php -m
  ```
- Проверить версию PHP:
  ```sh
  php -v
  ```

---