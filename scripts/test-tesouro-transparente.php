<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Services\TesouroTransparente;

$Data = TesouroTransparente::getLastAvailableDayData();
var_dump($Data);exit;
