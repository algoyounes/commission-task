#!/usr/bin/env php
<?php

use AlgoYounes\CommissionTask\Entity\Operation;
use AlgoYounes\CommissionTask\Services\Commission\CommissionCalculatorService;
use AlgoYounes\CommissionTask\Services\Commission\Rules\CommissionRulesRegistry;
use AlgoYounes\CommissionTask\Support\Reader\CSVReader;
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->load();

bcscale(10);

array_shift($argv);

if (count($argv) < 1) {
    echo "Please provide the file path as an argument." . PHP_EOL;
    exit(1);
}

$filePath = $argv[0];

if (! file_exists($filePath)) {
    echo "File not found: $filePath" . PHP_EOL;
    exit(1);
}

echo "Processing file: $filePath" . PHP_EOL;

$registry = new CommissionRulesRegistry();
$commissionCalculatorService = new CommissionCalculatorService($registry);

$csvReader = CSVReader::fromFilePath($filePath)
    ->withObject(Operation::class);

foreach ($csvReader->read() as $operation) {
    $commission = $commissionCalculatorService->calculateCommission($operation);

    echo $commission->getAmount() . PHP_EOL;
}
