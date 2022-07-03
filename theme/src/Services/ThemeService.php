<?php

namespace BlackCMS\Theme\Services;

use BlackCMS\Base\Supports\Helper;
use BlackCMS\Addon\Services\AddonService;
use BlackCMS\Setting\Repositories\Interfaces\SettingInterface;
use BlackCMS\Setting\Supports\SettingStore;
use BlackCMS\Theme\Events\ThemeRemoveEvent;
use BlackCMS\Widget\Repositories\Interfaces\WidgetInterface;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Theme;

class ThemeService
{
    /**
     * @var Filesystem
     */
    public $files;

    /**
     * @var SettingStore
     */
    public $settingStore;

    /**
     * @var AddonService
     */
    public $addonService;

    /**
     * @var WidgetInterface
     */
    public $widgetRepository;

    /**
     * @var SettingInterface
     */
    public $settingRepository;

    /**
     * ThemeService constructor.
     * @param Filesystem $files
     * @param SettingStore $settingStore
     * @param AddonService $addonService
     * @param WidgetInterface $widgetRepository
     * @param SettingInterface $settingRepository
     */
    public function __construct(
        Filesystem $files,
        SettingStore $settingStore,
        AddonService $addonService,
        WidgetInterface $widgetRepository,
        SettingInterface $settingRepository
    ) {
        $this->files = $files;
        $this->settingStore = $settingStore;
        $this->addonService = $addonService;
        $this->widgetRepository = $widgetRepository;
        $this->settingRepository = $settingRepository;
    }

    /**
     * @param string $theme
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function activate(string $theme): array
    {
        $validate = $this->validate($theme);

        if ($validate["error"]) {
            return $validate;
        }

        if (setting("theme") && $theme == Theme::getThemeName()) {
            return [
                "error" => true,
                "message" => trans(
                    "packages/theme::theme.theme_activated_already",
                    ["name" => $theme]
                ),
            ];
        }

        try {
            $content = get_file_data($this->getPath($theme, "composer.json"));

            if (!empty($content)) {
                $requiredAddons = Arr::get($content, "required_addons", []);
                if (!empty($requiredAddons)) {
                    foreach ($requiredAddons as $addon) {
                        $this->addonService->activate($addon);
                    }
                }
            }
        } catch (Exception $exception) {
            return [
                "error" => true,
                "message" => $exception->getMessage(),
            ];
        }

        Theme::setThemeName($theme);

        $published = $this->publishAssets($theme);

        if ($published["error"]) {
            return $published;
        }

        $this->settingStore->set("theme", $theme)->save();

        Helper::clearCache();

        return [
            "error" => false,
            "message" => trans("packages/theme::theme.active_success", [
                "name" => $theme,
            ]),
        ];
    }

    /**
     * @param string $theme
     * @return array
     */
    protected function validate(string $theme): array
    {
        $location = theme_path($theme);

        if (!$this->files->isDirectory($location)) {
            return [
                "error" => true,
                "message" => trans(
                    "packages/theme::theme.theme_is_not_existed"
                ),
            ];
        }

        if (!$this->files->exists($location . "/composer.json")) {
            return [
                "error" => true,
                "message" => trans("packages/theme::theme.missing_json_file"),
            ];
        }

        return [
            "error" => false,
            "message" => trans("packages/theme::theme.theme_invalid"),
        ];
    }

    /**
     * Get root writable path.
     *
     * @param string $theme
     * @param string|null $path
     * @return string
     */
    protected function getPath(string $theme, $path = null)
    {
        return rtrim(theme_path(), "/") .
            "/" .
            rtrim(ltrim(strtolower($theme), "/"), "/") .
            "/" .
            $path;
    }

    /**
     * @param string|null $theme
     * @return array
     */
    public function publishAssets(string $theme = null): array
    {
        if ($theme) {
            $themes = [$theme];
        } else {
            $themes = scan_folder(theme_path());
        }

        foreach ($themes as $theme) {
            $resourcePath = $this->getPath($theme, "public");

            $themePath = public_path("themes");
            if (!$this->files->isDirectory($themePath)) {
                $this->files->makeDirectory($themePath, 0755, true);
            } elseif (!$this->files->isWritable($themePath)) {
                return [
                    "error" => true,
                    "message" => trans(
                        "packages/theme::theme.folder_is_not_writeable",
                        ["name" => $themePath]
                    ),
                ];
            }

            $publishPath =
                $themePath .
                "/" .
                ($theme == Theme::getThemeName()
                    ? Theme::getPublicThemeName()
                    : $theme);

            if (!$this->files->isDirectory($publishPath)) {
                $this->files->makeDirectory($publishPath, 0755, true);
            }

            $this->files->copyDirectory($resourcePath, $publishPath);
        }

        return [
            "error" => false,
            "message" => trans(
                "packages/theme::theme.published_assets_success",
                ["themes" => implode(", ", $themes)]
            ),
        ];
    }

    /**
     * @param string $theme
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function remove(string $theme): array
    {
        $validate = $this->validate($theme);

        if ($validate["error"]) {
            return $validate;
        }

        if (Theme::getThemeName() == $theme) {
            return [
                "error" => true,
                "message" => trans(
                    "packages/theme::theme.cannot_remove_theme",
                    ["name" => $theme]
                ),
            ];
        }

        $this->removeAssets($theme);

        $this->files->deleteDirectory($this->getPath($theme), false);
        $this->widgetRepository->deleteBy(["theme" => $theme]);
        $this->settingRepository
            ->getModel()
            ->where("key", "like", "theme-" . $theme . "-%")
            ->delete();

        event(new ThemeRemoveEvent($theme));

        return [
            "error" => false,
            "message" => trans("packages/theme::theme.theme_deleted", [
                "name" => $theme,
            ]),
        ];
    }

    /**
     * @param string $theme
     * @return array
     */
    public function removeAssets(string $theme): array
    {
        $validate = $this->validate($theme);

        if ($validate["error"]) {
            return $validate;
        }

        $this->files->deleteDirectory(public_path("themes/" . $theme));

        return [
            "error" => false,
            "message" => trans("packages/theme::theme.removed_assets", [
                "name" => $theme,
            ]),
        ];
    }
}
