<?php

// Settings & Constants
if (!defined('ENGINE_FACTOR')) {
    define('ENGINE_FACTOR', 0.0008); // Fuel consumed per 1 HP per 1 KM
}
if (!defined('PASSENGER_FACTOR')) {
    define('PASSENGER_FACTOR', 0.001); // Extra fuel consumed per 1 passenger per 1 KM
}
if (!defined('DIESEL_EMISSION_FACTOR')) {
    define('DIESEL_EMISSION_FACTOR', 2.68); // Standard kg of CO2 released per 1 Liter of Diesel fuel
}
if (!defined('TERRAIN_MULTIPLIERS')) {
    define('TERRAIN_MULTIPLIERS', [
        'Flat' => 1.0,
        'Mountain' => 1.35
    ]);
}

/**
 * Calculates the CO2 Emission Rate (kg CO2/km) dynamically for a bus travel package.
 * 
 * @param int $busHp Horsepower of the assigned bus
 * @param int $totalSeats Total passenger capacity of the bus
 * @param string $fuelType Fuel type used ("Diesel", "EV", etc.)
 * @param string $terrainType Route terrain ("Flat" or "Mountain")
 * @return float Final emission rate rounded to 2 decimal places
 */
function calculateEmissionRate($busHp, $totalSeats, $fuelType, $terrainType) {
    // 1. Check for EV (Zero Tailpipe Emissions)
    if (strcasecmp($fuelType, 'EV') === 0) {
        return 0.0;
    }

    // 2. Calculate Total Fuel Consumed per 1 KM (Liters/km)
    $fuelPerKm = ($busHp * ENGINE_FACTOR) + ($totalSeats * PASSENGER_FACTOR);

    // 3. Apply the Fuel Emission Factor
    $baseEmissionRate = $fuelPerKm * DIESEL_EMISSION_FACTOR;

    // 4. Calculate Final Emission Rate (kg CO2/km)
    $terrainMultiplier = isset(TERRAIN_MULTIPLIERS[$terrainType]) ? TERRAIN_MULTIPLIERS[$terrainType] : 1.0;
    $finalEmissionRate = $baseEmissionRate * $terrainMultiplier;

    // Return the final emission rate rounded to 2 decimal places
    return round($finalEmissionRate, 2);
}
