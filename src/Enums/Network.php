<?php

declare(strict_types=1);

namespace Tigusigalpa\LunarCrush\Enums;

/**
 * Social networks supported by the LunarCrush Creator endpoints.
 */
enum Network: string
{
    case Twitter = 'twitter';
    case YouTube = 'youtube';
    case Instagram = 'instagram';
    case Reddit = 'reddit';
    case TikTok = 'tiktok';
}
