<?php

namespace BlackCMS\Addon\Services;

use BlackCMS\Base\Supports\Helper;
use BlackCMS\Addon\Events\ActivatedAddonEvent;
use BlackCMS\Setting\Supports\SettingStore;
use Composer\Autoload\ClassLoader;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class AddonService
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var SettingStore
     */
    protected $settingStore;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * AddonService constructor.
     * @param Application $app
     * @param SettingStore $settingStore
     * @param Filesystem $files
     */
    public function __construct(
        Application $app,
        SettingStore $settingStore,
        Filesystem $files
    ) {
        $this->app = $app;
        $this->settingStore = $settingStore;
        $this->files = $files;
    }

    /**
     * @param string $addon
     * @return array
     */
    public function activate(string $addon): array
    {
        $validate = $this->validate($addon);

        if ($validate["error"]) {
            return $validate;
        }

        $content = get_file_data(addon_path($addon) . "/composer.json");

        if (empty($content)) {
            return [
                "error" => true,
                "message" => trans("packages/addon::addon.invalid_json"),
            ];
        }

        if (!Arr::get($content, "ready", 1)) {
            return [
                "error" => true,
                "message" => trans("packages/addon::addon.addon_is_not_ready", [
                    "name" => Str::studly($addon),
                ]),
            ];
        }

        $activatedAddons = get_active_addons();

        if (!in_array($addon, $activatedAddons)) {
            if (!class_exists($content["extra"]["provider"])) {
                $loader = new ClassLoader();
                $loader->setPsr4(
                    $content["extra"]["namespace"],
                    addon_path($addon . "/src")
                );
                $loader->register(true);

                $published = $this->publishAssets($addon);

                if ($published["error"]) {
                    return $published;
                }

                if (class_exists($content["extra"]["namespace"] . "Addon")) {
                    call_user_func([
                        $content["extra"]["namespace"] . "Addon",
                        "activate",
                    ]);
                }

                if ($this->files->isDirectory(
                    addon_path($addon . "/database/migrations")
                )
                ) {
                    $this->app
                        ->make("migrator")
                        ->run(addon_path($addon . "/database/migrations"));
                }

                $this->app->register($content["extra"]["provider"]);
            }

            $this->settingStore
                ->set(
                    "activated_addons",
                    json_encode(
                        array_values(array_merge($activatedAddons, [$addon]))
                    )
                )
                ->save();

            if (class_exists($content["extra"]["namespace"] . "Addon")) {
                call_user_func([
                    $content["extra"]["namespace"] . "Addon",
                    "activated",
                ]);
            }

            Helper::clearCache();

            event(new ActivatedAddonEvent($addon));

            return [
                "error" => false,
                "message" => trans("packages/addon::addon.activate_success"),
            ];
        }

        return [
            "error" => true,
            "message" => trans("packages/addon::addon.activated_already"),
        ];
    }

    /**
     * @param string $addon
     * @return array
     */
    protected function validate(string $addon): array
    {
        $location = addon_path($addon);

        if (!$this->files->isDirectory($location)) {
            return [
                "error" => true,
                "message" => trans("packages/addon::addon.addon_not_exist"),
            ];
        }

        if (!$this->files->exists($location . "/composer.json")) {
            return [
                "error" => true,
                "message" => trans("packages/addon::addon.missing_json_file"),
            ];
        }

        return [
            "error" => false,
            "message" => trans("packages/addon::addon.addon_invalid"),
        ];
    }

    /**
     * @param string $addon
     * @return array
     */
    public function publishAssets(string $addon): array
    {
        $validate = $this->validate($addon);

        if ($validate["error"]) {
            return $validate;
        }

        $addonPath = public_path("vendor/core/addons");

        if (!$this->files->isDirectory($addonPath)) {
            $this->files->makeDirectory($addonPath, 0755, true);
        }

        if (!$this->files->isWritable($addonPath)) {
            return [
                "error" => true,
                "message" => trans(
                    "packages/addon::addon.folder_is_not_writeable",
                    ["name" => $addonPath]
                ),
            ];
        }

        if ($this->files->isDirectory(addon_path($addon . "/public"))) {
            $this->files->copyDirectory(
                addon_path($addon . "/public"),
                $addonPath . "/" . $addon
            );
        }

        return [
            "error" => false,
            "message" => trans(
                "packages/addon::addon.published_assets_success",
                ["name" => $addon]
            ),
        ];
    }

    /**
     * @param string $addon
     * @return array
     * @throws FileNotFoundException
     */
    public function remove(string $addon): array
    {
        $validate = $this->validate($addon);

        if ($validate["error"]) {
            return $validate;
        }

        $this->deactivate($addon);

        $location = addon_path($addon);

        if ($this->files->exists($location . "/composer.json")) {
            $content = get_file_data($location . "/composer.json");

            if (!empty($content)) {
                if (!class_exists($content["extra"]["provider"])) {
                    $loader = new ClassLoader();
                    $loader->setPsr4(
                        $content["extra"]["namespace"],
                        addon_path($addon . "/src")
                    );
                    $loader->register(true);
                }

                Schema::disableForeignKeyConstraints();
                if (class_exists($content["extra"]["namespace"] . "Addon")) {
                    call_user_func([
                        $content["extra"]["namespace"] . "Addon",
                        "remove",
                    ]);
                }
                Schema::enableForeignKeyConstraints();
            }
        }

        $migrations = [];
        foreach (scan_folder($location . "/database/migrations") as $file) {
            $migrations[] = pathinfo($file, PATHINFO_FILENAME);
        }

        DB::table("migrations")
            ->whereIn("migration", $migrations)
            ->delete();

        $this->files->deleteDirectory($location);

        if (empty($this->files->directories(addon_path()))) {
            $this->files->deleteDirectory(addon_path());
        }

        Helper::removeModuleFiles($addon, "addons");

        if (class_exists($content["extra"]["namespace"] . "Addon")) {
            call_user_func([
                $content["extra"]["namespace"] . "Addon",
                "removed",
            ]);
        }

        Helper::clearCache();

        return [
            "error" => false,
            "message" => trans("packages/addon::addon.addon_removed"),
        ];
    }

    /**
     * @param string $addon
     * @return array
     * @throws FileNotFoundException
     */
    public function deactivate(string $addon): array
    {
        $validate = $this->validate($addon);

        if ($validate["error"]) {
            return $validate;
        }

        $content = get_file_data(addon_path($addon) . "/composer.json");
        if (empty($content)) {
            return [
                "error" => true,
                "message" => trans("packages/addon::addon.invalid_json"),
            ];
        }

        if (!class_exists($content["extra"]["provider"])) {
            $loader = new ClassLoader();
            $loader->setPsr4(
                $content["extra"]["namespace"],
                addon_path($addon . "/src")
            );
            $loader->register(true);
        }

        $activatedAddons = get_active_addons();
        if (in_array($addon, $activatedAddons)) {
            if (class_exists($content["extra"]["namespace"] . "Addon")) {
                call_user_func([
                    $content["extra"]["namespace"] . "Addon",
                    "deactivate",
                ]);
            }
            if (($key = array_search($addon, $activatedAddons)) !== false) {
                unset($activatedAddons[$key]);
            }
            $this->settingStore
                ->set(
                    "activated_addons",
                    json_encode(array_values($activatedAddons))
                )
                ->save();

            if (class_exists($content["extra"]["namespace"] . "Addon")) {
                call_user_func([
                    $content["extra"]["namespace"] . "Addon",
                    "deactivated",
                ]);
            }

            Helper::clearCache();

            return [
                "error" => false,
                "message" => trans("packages/addon::addon.deactivated_success"),
            ];
        }

        return [
            "error" => true,
            "message" => trans("packages/addon::addon.deactivated_already"),
        ];
    }
}
