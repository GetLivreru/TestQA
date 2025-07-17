<?php
require_once 'main.php';

echo "=== Тестирование функции haversineGreatCircleDistance ===\n\n";

// Тест 1: Расстояние между Москвой и Санкт-Петербургом
echo "Тест 1: Москва -> Санкт-Петербург\n";
$moscow_lat = 55.7558;
$moscow_lon = 37.6176;
$spb_lat = 59.9311;
$spb_lon = 30.3609;

$distance1 = haversineGreatCircleDistance($moscow_lat, $moscow_lon, $spb_lat, $spb_lon);
echo "Расстояние: " . round($distance1) . " метров\n";
echo "Расстояние: " . round($distance1 / 1000, 2) . " км\n";
echo "Ожидаемое расстояние: ~635 км\n\n";

// Тест 2: Расстояние между Киевом и Москвой  
echo "Тест 2: Киев -> Москва\n";
$kiev_lat = 50.4501;
$kiev_lon = 30.5234;

$distance2 = haversineGreatCircleDistance($kiev_lat, $kiev_lon, $moscow_lat, $moscow_lon);
echo "Расстояние: " . round($distance2) . " метров\n";
echo "Расстояние: " . round($distance2 / 1000, 2) . " км\n";
echo "Ожидаемое расстояние: ~757 км\n\n";

// Тест 3: Расстояние между одинаковыми точками (должно быть 0)
echo "Тест 3: Одинаковые координаты\n";
$distance3 = haversineGreatCircleDistance($moscow_lat, $moscow_lon, $moscow_lat, $moscow_lon);
echo "Расстояние: " . round($distance3) . " метров\n";
echo "Ожидаемое расстояние: 0 метров\n\n";

// Тест 4: Расстояние между Нью-Йорком и Лондоном
echo "Тест 4: Нью-Йорк -> Лондон\n";
$ny_lat = 40.7128;
$ny_lon = -74.0060;
$london_lat = 51.5074;
$london_lon = -0.1278;

$distance4 = haversineGreatCircleDistance($ny_lat, $ny_lon, $london_lat, $london_lon);
echo "Расстояние: " . round($distance4) . " метров\n";
echo "Расстояние: " . round($distance4 / 1000, 2) . " км\n";
echo "Ожидаемое расстояние: ~5570 км\n\n";

// Тест 5: Проверка с другим радиусом Земли
echo "Тест 5: Москва -> СПб с радиусом в милях\n";
$distance5 = haversineGreatCircleDistance($moscow_lat, $moscow_lon, $spb_lat, $spb_lon, 3959); // радиус в милях
echo "Расстояние: " . round($distance5, 2) . " миль\n";
echo "Ожидаемое расстояние: ~394 мили\n\n";

echo "=== Тестирование завершено ===\n";
?> 