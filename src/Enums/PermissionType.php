<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Enums;

enum PermissionType: string
{
    case resources = 'resources';
    case pages = 'pages';
    case widgets = 'widgets';
    case customs = 'customs';
    case panels = 'panels';
}
