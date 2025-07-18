<?php

use PHPUnit\Framework\TestCase;

class HaversineDistanceTest extends TestCase
{
    public function testMoscowToStPetersburg(): void
    {
        $moscow_lat = 55.7558;
        $moscow_lon = 37.6176;
        $spb_lat = 59.9311;
        $spb_lon = 30.3609;
        $distance = haversineGreatCircleDistance($moscow_lat, $moscow_lon, $spb_lat, $spb_lon);
        $this->assertEqualsWithDelta(631752, $distance, 1, 'Расстояние Москва-СПб должно быть ~631.8 км');
    }
    public function testKievToMoscow(): void
    {
        $kiev_lat = 50.4501;
        $kiev_lon = 30.5234;
        $moscow_lat = 55.7558;
        $moscow_lon = 37.6176;
        $distance = haversineGreatCircleDistance($kiev_lat, $kiev_lon, $moscow_lat, $moscow_lon);
        $this->assertEqualsWithDelta(755753, $distance, 1, 'Расстояние Киев-Москва должно быть ~755.8 км');
    }
    public function testIdenticalCoordinates(): void
    {
        $lat = 55.7558;
        $lon = 37.6176;
        $distance = haversineGreatCircleDistance($lat, $lon, $lat, $lon);
        $this->assertEquals(0, $distance, 0.001, 'Расстояние между одинаковыми точками должно быть 0');
    }
    public function testNewYorkToLondon(): void
    {
        $ny_lat = 40.7128;
        $ny_lon = -74.0060;
        $london_lat = 51.5074;
        $london_lon = -0.1278;
        $distance = haversineGreatCircleDistance($ny_lat, $ny_lon, $london_lat, $london_lon);
        $this->assertEqualsWithDelta(5570222, $distance, 1, 'Расстояние Нью-Йорк-Лондон должно быть ~5570.2 км');
    }
    public function testWithMileRadius(): void
    {
        $moscow_lat = 55.7558;
        $moscow_lon = 37.6176;
        $spb_lat = 59.9311;
        $spb_lon = 30.3609;
        $distance = haversineGreatCircleDistance($moscow_lat, $moscow_lon, $spb_lat, $spb_lon, 3959);
        $this->assertEqualsWithDelta(392.577, $distance, 0.001, 'Расстояние в милях должно быть ~392.58');
    }
    public function testEquatorialPoints(): void
    {
        $lat1 = 0;
        $lon1 = 0;
        $lat2 = 0;
        $lon2 = 90;
        $distance = haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2);
        $this->assertEqualsWithDelta(10007543.398, $distance, 0.001, 'Расстояние между точками на экваторе через 90° должно быть ~10007.5 км');
    }
    public function testEquatorToPole(): void
    {
        $lat1 = 0;
        $lon1 = 0;
        $lat2 = 90;
        $lon2 = 0;
        $distance = haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2);
        $this->assertEqualsWithDelta(10007543.398, $distance, 0.001, 'Расстояние от экватора до полюса должно быть ~10007.5 км');
    }
    public function testAntipodes(): void
    {
        $lat1 = 45;
        $lon1 = 0;
        $lat2 = -45;
        $lon2 = 180;
        $distance = haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2);
        $this->assertEqualsWithDelta(20015086.796, $distance, 0.001, 'Расстояние между антиподами должно быть ~20015 км');
    }
    public function testSmallDistance(): void
    {
        $lat1 = 55.7558;
        $lon1 = 37.6176;
        $lat2 = 55.7608;
        $lon2 = 37.6256;
        $distance = haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2);
        $this->assertEqualsWithDelta(748.098, $distance, 0.001, 'Небольшое расстояние должно быть ~748 м');
    }
    public function testBoundaryCoordinates(): void
    {
        $lat1 = -90;
        $lon1 = -180;
        $lat2 = 90;
        $lon2 = 180;
        $distance = haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2);
        $this->assertEqualsWithDelta(20015086.796, $distance, 0.001, 'Расстояние между крайними точками должно быть ~20015 км');
    }
}