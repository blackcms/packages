<?php

namespace BlackCMS\Page\Services;

use BaseHelper;
use BlackCMS\Base\Enums\BaseStatusEnum;
use BlackCMS\Page\Models\Page;
use BlackCMS\Page\Repositories\Interfaces\PageInterface;
use BlackCMS\Seo\SeoOpenGraph;
use Eloquent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use MediaManagement;
use Seo;
use Theme;

class PageService
{
    /**
     * @param Eloquent $slug
     * @return array|Eloquent
     */
    public function handleFrontRoutes($slug)
    {
        if (!$slug instanceof Eloquent) {
            return $slug;
        }

        $condition = [
            "id" => $slug->reference_id,
            "status" => BaseStatusEnum::PUBLISHED,
        ];

        if (Auth::check() && request()->input("preview")) {
            Arr::forget($condition, "status");
        }

        if ($slug->reference_type !== Page::class) {
            return $slug;
        }

        $page = app(PageInterface::class)->getFirstBy(
            $condition,
            ["*"],
            ["slugable"]
        );

        if (empty($page)) {
            abort(404);
        }

        $meta = new SeoOpenGraph();
        if ($page->image) {
            $meta->setImage(MediaManagement::getImageUrl($page->image));
        }

        if (!BaseHelper::isHomepage($page->id)) {
            Seo::setTitle($page->name)->setDescription($page->description);

            $meta->setTitle($page->name);
            $meta->setDescription($page->description);
        } else {
            $siteTitle = theme_option("seo_title")
                ? theme_option("seo_title")
                : theme_option("site_title");
            $seoDescription = theme_option("seo_description");

            Seo::setTitle($siteTitle)->setDescription($seoDescription);

            $meta->setTitle($siteTitle);
            $meta->setDescription($seoDescription);
        }

        $meta->setUrl($page->url);
        $meta->setType("article");

        Seo::setSeoOpenGraph($meta);

        if ($page->template) {
            Theme::uses(Theme::getThemeName())->layout($page->template);
        }

        if (function_exists("admin_bar") &&
            Auth::check() &&
            Auth::user()->hasPermission("pages.edit")
        ) {
            admin_bar()->registerLink(
                trans("packages/page::pages.edit_this_page"),
                route("pages.edit", $page->id)
            );
        }

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PAGE_MODULE_NAME, $page);

        Theme::breadcrumb()
            ->add(__("Home"), route("public.index"))
            ->add(Seo::getTitle(), $page->url);

        return [
            "view" => "page",
            "default_view" => "packages/page::themes.page",
            "data" => compact("page"),
            "slug" => $page->slug,
        ];
    }
}
