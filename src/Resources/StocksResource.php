<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Resources;

use Tigusigalpa\LunarCrush\Collections\StockCollection;
use Tigusigalpa\LunarCrush\Collections\TimeSeriesCollection;
use Tigusigalpa\LunarCrush\Dto\StockDto;
use Tigusigalpa\LunarCrush\Dto\TimeSeriesPointDto;

/**
 * Fluent builder for the LunarCrush Stocks endpoint group.
 */
final class StocksResource extends AbstractResource
{
    /**
     * List all stocks. `GET /public/stocks/list/v1`.
     *
     * @return StockCollection|StockDto[]
     */
    public function list(): static
    {
        return $this->reset('/public/stocks/list/v1')->asCollection(StockDto::class, StockCollection::class);
    }

    /**
     * List all stocks, near real-time. `GET /public/stocks/list/v2`.
     *
     * @return StockCollection|StockDto[]
     */
    public function listV2(): static
    {
        return $this->reset('/public/stocks/list/v2')->asCollection(StockDto::class, StockCollection::class);
    }

    /**
     * Fetch a single stock's detail. `GET /public/stocks/:stock/v1`.
     */
    public function stock(string $stock): static
    {
        return $this->reset('/public/stocks/' . $this->encodePathSegment($stock) . '/v1')->asItem(StockDto::class);
    }

    /**
     * Fetch time-series history for a stock. `GET /public/stocks/:stock/time-series/v2`.
     *
     * @return TimeSeriesCollection|TimeSeriesPointDto[]
     */
    public function timeSeries(string $stock): static
    {
        return $this->reset('/public/stocks/' . $this->encodePathSegment($stock) . '/time-series/v2')
            ->asCollection(TimeSeriesPointDto::class, TimeSeriesCollection::class);
    }
}
