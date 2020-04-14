<?php namespace Inetis\Revisionable;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Revisionable',
            'description' => 'Add revision history to backend',
            'author' => 'inetis',
            'icon' => 'icon-history',
        ];
    }

    public function register()
    {
        $this->registerConsoleCommand('revisionable.clear', Console\ClearEmptyRevisions::class);
    }

    /**
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     * @see https://laravel.com/docs/5.5/scheduling#schedule-frequency-options
     */
    public function registerSchedule($schedule)
    {
        $schedule->command(Console\ClearEmptyRevisions::class)->daily();
    }
}
