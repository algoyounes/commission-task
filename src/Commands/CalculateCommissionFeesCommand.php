<?php

namespace AlgoYounes\CommissionTask\Commands;

use AlgoYounes\CommissionTask\Entity\Operation;
use AlgoYounes\CommissionTask\Services\Commission\CommissionCalculatorService;
use AlgoYounes\CommissionTask\Services\Commission\Rules\CommissionRulesRegistry;
use AlgoYounes\CommissionTask\Support\Reader\CSVReader;

class CalculateCommissionFeesCommand
{
    private CommissionCalculatorService $commissionCalculatorService;

    public function __construct()
    {
        $registry = new CommissionRulesRegistry();
        $this->commissionCalculatorService = new CommissionCalculatorService($registry);
    }

    public function handle(string $filePath): void
    {
        $csvReader = CSVReader::fromFilePath($filePath)
            ->withObject(Operation::class);

        foreach ($csvReader->read() as $operation) {
            $commission = $this->commissionCalculatorService->calculateCommission($operation);

            echo $commission->getAmount() . PHP_EOL;
        }
    }
}
