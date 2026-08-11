<?php

namespace Tests;

use BladeUI\Icons\BladeIconsServiceProvider;
use Livewire\LivewireServiceProvider;
use MallardDuck\LucideIcons\BladeLucideIconsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Sigta\Ui\SigtaServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            BladeIconsServiceProvider::class,
            BladeLucideIconsServiceProvider::class,
            SigtaServiceProvider::class,
        ];
    }
}
