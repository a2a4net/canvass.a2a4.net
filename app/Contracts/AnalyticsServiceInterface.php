<?php

namespace App\Contracts;

interface AnalyticsServiceInterface
{
    public function getData(array $filters);

    public function getTableView();
}
