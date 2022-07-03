<?php

namespace BlackCMS\Addon\Commands;

use BlackCMS\Addon\Services\AddonService;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class AddonRemoveCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = "cms:addon:remove {name : The addon that you want to remove} {--force : Force to remove addon without confirmation}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Remove a addon in the /addons directory.";

    /**
     * @var AddonService
     */
    protected $addonService;

    /**
     * AddonRemoveCommand constructor.
     * @param AddonService $addonService
     */
    public function __construct(AddonService $addonService)
    {
        parent::__construct();

        $this->addonService = $addonService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirmToProceed(
            "Are you sure you want to permanently delete?",
            true
        )
        ) {
            return 1;
        }

        if (!preg_match('/^[a-z0-9\-]+$/i', $this->argument("name"))) {
            $this->error("Only alphabetic characters are allowed.");
            return 1;
        }

        $addon = strtolower($this->argument("name"));
        $result = $this->addonService->remove($addon);

        if ($result["error"]) {
            $this->error($result["message"]);
            return 1;
        }

        $this->info($result["message"]);

        return 0;
    }
}
