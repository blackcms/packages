<?php

namespace BlackCMS\Addon\Commands;

use BlackCMS\Addon\Services\AddonService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class AddonActivateCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = "cms:addon:activate {name : The addon that you want to activate}";
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Activate a addon in /addons directory";

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
        if (!preg_match('/^[a-z0-9\-]+$/i', $this->argument("name"))) {
            $this->error("Only alphabetic characters are allowed.");
            return 1;
        }

        $addon = strtolower($this->argument("name"));

        $result = $this->addonService->activate($addon);

        if ($result["error"]) {
            $this->error($result["message"]);
            return 1;
        }

        $this->info($result["message"]);

        return 0;
    }
}
