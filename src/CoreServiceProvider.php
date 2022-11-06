<?php

namespace LaraPlatform\Core;

use Illuminate\Support\ServiceProvider;
use LaraPlatform\Core\Commands\CoreCommand;
use LaraPlatform\Core\Facades\Theme;
use LaraPlatform\Core\Loader\OptionLoader;
use LaraPlatform\Core\Loader\TableLoader;
use LaraPlatform\Core\Supports\ServicePackage;
use LaraPlatform\Core\Traits\WithServiceProvider;
use LaraPlatform\Core\Builder\Menu\MenuBuilder;
use LaraPlatform\Core\Supports\BaseScan;
use Illuminate\Support\Collection;

class CoreServiceProvider extends ServiceProvider
{
    use WithServiceProvider;

    public function configurePackage(ServicePackage $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         */
        $package
            ->name('core')
            ->hasConfigFile()
            ->hasViews()
            ->hasHelpers()
            ->hasAssets()
            ->hasTranslations()
            ->runsMigrations()
            ->hasRoutes('web')
            ->hasCommand(CoreCommand::class);
    }
    public function extending()
    {
        // Collection::macro('links', function () {
        //     return $this->links();
        // });
    }
    public function registerMenu()
    {
        add_menu_item('core::menu.sidebar.dashboard', 'bi bi-speedometer', '', 'admin.dashboard', MenuBuilder::ItemRouter);
        add_menu_item('core::menu.sidebar.theme', 'bi bi-speedometer', '', 'theme');

        add_menu_with_sub(function ($subItem) {
            $subItem
                ->addItem('core::menu.sidebar.user', 'bi bi-speedometer', '', ['name' => 'admin.table.slug', 'param' => ['module' => 'user']], MenuBuilder::ItemRouter)
                ->addItem('core::menu.sidebar.role', 'bi bi-speedometer', '', ['name' => 'admin.table.slug', 'param' => ['module' => 'role']], MenuBuilder::ItemRouter)
                ->addItem('core::menu.sidebar.permission', 'bi bi-speedometer', '', ['name' => 'admin.table.slug', 'param' => ['module' => 'permission']], MenuBuilder::ItemRouter);
        }, 'core::menu.sidebar.user',  'bi bi-speedometer');
        add_menu_with_sub(function ($subItem) {
            $subItem->addItem('core::menu.sidebar.setting', 'bi bi-speedometer', '', ['name' => 'admin.option', 'param' => []], MenuBuilder::ItemRouter)
                ->addItem('core::menu.sidebar.module', 'bi bi-speedometer', '', ['name' => 'admin.table.slug', 'param' => ['module' => 'module']], MenuBuilder::ItemRouter)
                ->addItem('core::menu.sidebar.plugin', 'bi bi-speedometer', '', 'plugin');
        }, 'core::menu.sidebar.setting', 'bi bi-speedometer');
    }
    public function packageRegistered()
    {
        add_link_symbolic(__DIR__ . '/../public', public_path('modules/lara-core'));
        add_asset_js(asset('modules/lara-core/js/lara-core.js'), '', 0);
        add_asset_css(asset('modules/lara-core/css/lara-core.css'), '',  0);
        Theme::Register(__DIR__ . '/../themes');
        Theme::active('lara-admin');
        Theme::print();
        TableLoader::load(__DIR__ . '/../config/tables');
        OptionLoader::load(__DIR__ . '/../config/options');
        $this->registerMenu();
        $this->extending();
    }
}
