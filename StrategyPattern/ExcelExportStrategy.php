<?php

namespace App\Strategies;

use App\Contracts\Strategy\ExportStrategyContract;
use App\Contracts\Strategy\StrategyContract;
use App\Filters\QueryFilter;
use App\Strategies\Modules\BillingIncomeExportStrategy;
use App\Strategies\Modules\BillingOutcomeExportStrategy;
use App\Strategies\Modules\ClientContractExportStrategy;
use App\Strategies\Modules\IndividualClientsExportStrategy;
use App\Strategies\Modules\InvestationsExportStrategy;
use App\Strategies\Modules\InvestationTypesExportStrategy;
use App\Strategies\Modules\LegalClientsExportStrategy;
use App\Strategies\Modules\ReinsuranceIncomeExportStrategy;
use App\Strategies\Modules\ReinsuranceOutcomeExportStrategy;
use App\Strategies\Modules\ReinsurersExportStrategy;
use Exception;

class ExcelExportStrategy
{
    private string $strategy_name;
    private ExportStrategyContract $strategy;

    private array $strategies = [
        BillingIncomeExportStrategy::class,
        BillingOutcomeExportStrategy::class,
        ClientContractExportStrategy::class,
        IndividualClientsExportStrategy::class,
        InvestationsExportStrategy::class,
        InvestationTypesExportStrategy::class,
        LegalClientsExportStrategy::class,
        ReinsuranceIncomeExportStrategy::class,
        ReinsuranceOutcomeExportStrategy::class,
        ReinsurersExportStrategy::class,
    ];

    /**
     * @return array
     */
    public function getModuleNames(): array
    {
        $modules = [];

        foreach ($this->strategies as $strategy) {
            $modules[] = $strategy::getStrategyName();
        }

        return $modules;
    }

    /**
     * @param ExportStrategyContract $strategy
     */
    public function setStrategy(ExportStrategyContract $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * @param string $strategy_name
     * @return $this
     */
    public function setStrategyName(string $strategy_name): ExcelExportStrategy
    {
        $this->strategy_name = $strategy_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getStrategyName(): string
    {
        return $this->strategy_name;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function execute()
    {
        $strategy = $this->getStrategy($this->getStrategyName());

        $this->setStrategy($strategy);

        return $this->strategy->generate();
    }

    /**
     * @param string $strategy_name
     * @return ExportStrategyContract
     * @throws Exception
     */
    private function getStrategy(string $strategy_name): ExportStrategyContract
    {
        /**
         * @var ExportStrategyContract $strategy
         */
        foreach ($this->strategies as $strategy) {
            if ($strategy::getStrategyName() === $strategy_name) {
                return new $strategy;
            }
        }

        throw new Exception('Strategy not found for this type');
    }
}
