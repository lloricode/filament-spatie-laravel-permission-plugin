<?php

declare(strict_types=1);

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'rd', 'die', 'eval', 'sleep'])
    ->each->not->toBeUsed();

arch()
    ->preset()
    ->laravel();

arch()
    ->preset()
    ->php()
    ->ignoring(['debug_backtrace']);

arch()
    ->preset()
    ->security();
