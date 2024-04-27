<?php

namespace App\Strategies\Modules;

use App\Contracts\Strategy\ExportStrategyContract;
use App\Exports\ReinsurerReportExport;
use App\Filters\QueryFilter;
use App\Filters\ReinsurerFilter;
use App\Models\Reinsurer;
use Exception;
use Maatwebsite\Excel\Excel;

class ReinsurersExportStrategy implements ExportStrategyContract
{

    /**
     * @inheritDoc
     */
    public static function getStrategyName(): string
    {
        return 'reinsurers';
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function generate()
    {
        $filter = $this->getQueryFilter();
        $reinsurers = Reinsurer::query()->filter($filter);

        $result = (new ReinsurerReportExport($reinsurers))
            ->store('reports/' . $path = static::getStrategyName() . '.xlsx', 'public',Excel::XLSX);

        if (! $result) {
            throw new Exception('Unhandled error');
        }

        return $path;
    }

    public function getQueryFilter(): QueryFilter
    {
        return new ReinsurerFilter(request()->input('params', []));
    }
}
