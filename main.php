<?php

/**
 * Вычисляет расстояние между двумя точками на сфере используя формулу Haversine
 * 
 * Формула Haversine используется для вычисления расстояния по великому кругу
 * между двумя точками на поверхности сферы по их координатам широты и долготы.
 * 
 * @param float $latitudeFrom   Широта первой точки в градусах
 * @param float $longitudeFrom  Долгота первой точки в градусах  
 * @param float $latitudeTo     Широта второй точки в градусах
 * @param float $longitudeTo    Долгота второй точки в градусах
 * @param float $earthRadius    Радиус Земли в метрах (по умолчанию 6371000 м)
 * 
 * @return float Расстояние между точками в единицах радиуса (по умолчанию в метрах)
 * 
 * @example
 * // Расстояние между Москвой и Санкт-Петербургом
 * $distance = haversineGreatCircleDistance(55.7558, 37.6176, 59.9311, 30.3609);
 * echo $distance; // ~635000 метров
 * 
 * @author Ваше имя
 * @since 1.0.0
 */
function haversineGreatCircleDistance(
    float $latitudeFrom,
    float $longitudeFrom,
    float $latitudeTo,
    float $longitudeTo,
    float $earthRadius = 6371000
): float {
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

    return $angle * $earthRadius;
}
