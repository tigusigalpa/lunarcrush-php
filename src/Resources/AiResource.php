<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Resources;

use Tigusigalpa\LunarCrush\Enums\Network;

/**
 * Fluent builder for the LunarCrush AI & MCP endpoint group.
 */
final class AiResource extends AbstractResource
{
    /**
     * Fetch an AI-generated insight for a topic. `GET /public/ai/topic/:topic`.
     *
     * @return array<string, mixed>
     */
    public function topic(string $topic): static
    {
        return $this->reset('/public/ai/topic/' . $this->encodePathSegment($topic));
    }

    /**
     * Fetch an AI-generated insight for a creator. `GET /public/ai/creator/:network/:id`.
     *
     * @param Network|string $network Social network (`twitter`, `youtube`, `instagram`, `reddit`, `tiktok`).
     * @param string         $id      Creator identifier on the given network.
     *
     * @return array<string, mixed>
     */
    public function creator(Network|string $network, string $id): static
    {
        $network = $network instanceof Network ? $network->value : $network;

        return $this->reset('/public/ai/creator/' . $this->encodePathSegment($network) . '/' . $this->encodePathSegment($id));
    }
}
