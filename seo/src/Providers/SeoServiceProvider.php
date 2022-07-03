<?php

namespace BlackCMS\Seo\Providers;

use BlackCMS\Base\Traits\LoadAndPublishDataTrait;
use BlackCMS\Seo\Contracts\SeoContract;
use BlackCMS\Seo\Contracts\SeoMetaContract;
use BlackCMS\Seo\Contracts\SeoOpenGraphContract;
use BlackCMS\Seo\Contracts\SeoTwitterContract;
use BlackCMS\Seo\Seo;
use BlackCMS\Seo\SeoMeta;
use BlackCMS\Seo\SeoOpenGraph;
use BlackCMS\Seo\SeoTwitter;
use Illuminate\Support\ServiceProvider;

/**
 * @since 02/12/2015 14:09 PM
 */
class SeoServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(SeoMetaContract::class, SeoMeta::class);
        $this->app->bind(SeoContract::class, Seo::class);
        $this->app->bind(SeoOpenGraphContract::class, SeoOpenGraph::class);
        $this->app->bind(SeoTwitterContract::class, SeoTwitter::class);

        $this->setNamespace("packages/seo")->loadHelpers();
    }

    public function boot()
    {
        $this->loadAndPublishConfigurations(["general"])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssets();

        $this->app->register(EventServiceProvider::class);

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
