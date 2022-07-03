<?php

namespace BlackCMS\Addon\Http\Controllers;

use Assets;
use BlackCMS\Base\Http\Responses\BaseHttpResponse;
use BlackCMS\Addon\Services\AddonService;
use Exception;
use File;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class AddonController extends Controller
{
    /**
     * Show all addons in system
     * @return Application|Factory|View
     */
    public function index()
    {
        page_title()->setTitle(trans("packages/addon::addon.addons"));

        Assets::addScriptsDirectly(
            "vendor/core/packages/addon/js/addon.js"
        )->addStylesDirectly("vendor/core/packages/addon/css/addon.css");

        $list = [];

        if (File::exists(addon_path(".DS_Store"))) {
            File::delete(addon_path(".DS_Store"));
        }

        $addons = scan_folder(addon_path());
        if (!empty($addons)) {
            $installed = get_active_addons();
            foreach ($addons as $addon) {
                if (File::exists(addon_path($addon . "/.DS_Store"))) {
                    File::delete(addon_path($addon . "/.DS_Store"));
                }

                $addonPath = addon_path($addon);
                if (!File::isDirectory($addonPath) ||
                    !File::exists($addonPath . "/composer.json")
                ) {
                    continue;
                }

                $content = get_file_data($addonPath . "/composer.json");

                if (!empty($content)) {
                    if (!is_array($installed) ||
                        !in_array($addon, $installed)
                    ) {
                        $content["status"] = 0;
                    } else {
                        $content["status"] = 1;
                    }

                    $content["path"] = $addon;
                    $list[] = (object) $content;
                }
            }
        }

        $list = collect($list)
            ->sortBy("status")
            ->reverse()
            ->toArray();

        return view("packages/addon::index", compact("list"));
    }

    /**
     * Activate or Deactivate addon
     *
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param AddonService $addonService
     * @return BaseHttpResponse
     */
    public function update(
        Request $request,
        BaseHttpResponse $response,
        AddonService $addonService
    ) {
        $addon = strtolower($request->input("name"));

        $content = get_file_data(addon_path($addon . "/composer.json"));

        if (empty($content)) {
            return $response
                ->setError()
                ->setMessage(trans("packages/addon::addon.invalid_addon"));
        }

        $content["name"] = $content["extra"]["name"];
        try {
            $activatedAddons = get_active_addons();
            if (!in_array($addon, $activatedAddons)) {
                $result = $addonService->activate($addon);

                $migrator = app("migrator");
                $migrator->run(database_path("migrations"));

                $paths = [core_path(), package_path()];

                foreach ($paths as $path) {
                    foreach (scan_folder($path) as $module) {
                        if ($path == addon_path() &&
                            !is_addon_active($module)
                        ) {
                            continue;
                        }

                        $modulePath = $path . "/" . $module;

                        if (!File::isDirectory($modulePath)) {
                            continue;
                        }

                        if (File::isDirectory(
                            $modulePath . "/database/migrations"
                        )
                        ) {
                            $migrator->run(
                                $modulePath . "/database/migrations"
                            );
                        }
                    }
                }
            } else {
                $result = $addonService->deactivate($addon);
            }

            if ($result["error"]) {
                return $response->setError()->setMessage($result["message"]);
            }

            return $response->setMessage(
                trans("packages/addon::addon.update_addon_status_success")
            );
        } catch (Exception $exception) {
            return $response->setError()->setMessage($exception->getMessage());
        }
    }

    /**
     * Remove addon
     *
     * @param string $addon
     * @param BaseHttpResponse $response
     * @param AddonService $addonService
     * @return BaseHttpResponse
     */
    public function destroy(
        $addon,
        BaseHttpResponse $response,
        AddonService $addonService
    ) {
        $addon = strtolower($addon);

        try {
            $result = $addonService->remove($addon);

            if ($result["error"]) {
                return $response->setError()->setMessage($result["message"]);
            }

            return $response->setMessage(
                trans("packages/addon::addon.remove_addon_success")
            );
        } catch (Exception $exception) {
            return $response->setError()->setMessage($exception->getMessage());
        }
    }
}
