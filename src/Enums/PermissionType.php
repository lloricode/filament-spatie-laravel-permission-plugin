<?php

declare(strict_types=1);

namespace Lloricode\FilamentSpatieLaravelPermissionPlugin\Enums;

enum PermissionType: string
{
    case resources = 'resources';
    case panels = 'panels';
    case pages = 'pages';
    case widgets = 'widgets';
    case customs = 'customs';
}
