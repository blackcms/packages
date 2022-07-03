<?php

namespace BlackCMS\Addon\Commands;

use BlackCMS\Addon\Services\AddonService;
use File;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class AddonDeactivateAllCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = "cms:addon:deactivate:all";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Deactivate all addons in /addons directory";

    /**
     * @var AddonService
     */
    protected $addonService;

    /**
     * AddonActivateCommand constructor.
     * @param AddonService $addonService
     */
    public function __construct(AddonService $addonService)
    {
        parent::__construct();

        $this->addonService = $addonService;
    }

    /**
     * @return boolean
     * @throws FileNotFoundException
     */
    public function handle()
    {
        foreach (scan_folder(addon_path()) as $addon) {
            $this->addonService->deactivate($addon);
        }

        $this->info("Deactivated successfully!");

        return 0;
    }
}
