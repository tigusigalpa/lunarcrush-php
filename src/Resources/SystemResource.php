<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Resources;

/**
 * Fluent builder for the LunarCrush System endpoint group.
 */
final class SystemResource extends AbstractResource
{
    /**
     * Fetch the historical data change log. `GET /public/system/changes`.
     *
     * @return array<string, mixed>
     */
    public function changes(): static
    {
        return $this->reset('/public/system/changes');
    }
}
