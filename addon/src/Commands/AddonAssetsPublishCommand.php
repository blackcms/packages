<?php

namespace BlackCMS\Addon\Commands;

use BlackCMS\Addon\Services\AddonService;
use Illuminate\Console\Command;

class AddonAssetsPublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "cms:addon:assets:publish {name : The addon that you want to publish assets}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish assets for a addon";

    /**
     * @var AddonService
     */
    protected $addonService;

    /**
     * AddonAssetsPublishCommand constructor.
     * @param AddonService $addonService
     */
    public function __construct(AddonService $addonService)
    {
        parent::__construct();

        $this->addonService = $addonService;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle()
    {
        if (!preg_match('/^[a-z0-9\-]+$/i', $this->argument("name"))) {
            $this->error("Only alphabetic characters are allowed.");
            return 1;
        }

        $addon = strtolower($this->argument("name"));
        $result = $this->addonService->publishAssets($addon);

        if ($result["error"]) {
            $this->error($result["message"]);
            return 1;
        }

        $this->info($result["message"]);

        return 0;
    }
}
