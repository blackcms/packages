<?php

namespace BlackCMS\Optimize\Providers;

use BlackCMS\Base\Traits\LoadAndPublishDataTrait;
use BlackCMS\Optimize\Http\Middleware\CollapseWhitespace;
use BlackCMS\Optimize\Http\Middleware\ElideAttributes;
use BlackCMS\Optimize\Http\Middleware\InlineCss;
use BlackCMS\Optimize\Http\Middleware\InsertDNSPrefetch;
use BlackCMS\Optimize\Http\Middleware\RemoveComments;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class OptimizeServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot()
    {
        $this->setNamespace("packages/optimize")
            ->loadAndPublishConfigurations(["general"])
            ->loadAndPublishTranslations()
            ->loadAndPublishViews();

        /**
         * @var Router $router
         */
        $router = $this->app["router"];

        $router->pushMiddlewareToGroup("web", ElideAttributes::class);
        $router->pushMiddlewareToGroup("web", InsertDNSPrefetch::class);
        if (!$this->app->environment("local") && !is_in_admin()) {
            $router->pushMiddlewareToGroup("web", CollapseWhitespace::class);
        }
        $router->pushMiddlewareToGroup("web", RemoveComments::class);
        $router->pushMiddlewareToGroup("web", InlineCss::class);

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
