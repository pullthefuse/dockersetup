<?php

namespace App\Twig;

use App\Helper\Str;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SlugExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('slug', static function ($value) {
                return Str::slug($value);
            })
        ];
    }
}
