<?php

use PHPUnit\Framework\TestCase;

/**
 * Класс для тестирования функции haversineGreatCircleDistance
 */
class HaversineDistanceTest extends TestCase
{
    public function testIdenticalCoordinates(): void
    {
        $lat = 55.7558;
        $lon = 37.6176;
        
        $distance = haversineGreatCircleDistance($lat, $lon, $lat, $lon);
        
        $this->assertEquals(0, $distance, 0.001, 'Расстояние между одинаковыми точками должно быть 0');
    }
    
}