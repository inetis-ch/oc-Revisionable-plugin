<?php namespace Inetis\Revisionable\Console;

use Illuminate\Console\Command;
use System\Models\Revision;

class ClearEmptyRevisions extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'revisions:clear';

    /**
     * @var string The console command description.
     */
    protected $description = 'Clear empty revisions (e.g.: null to empty string, ...)';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Revision::query()
            ->where(function ($query) {
                $query->whereNull('old_value')->orWhere('old_value', '');
            })
            ->where(function ($query) {
                $query->whereNull('new_value')->orWhere('new_value', '');
            })
            ->delete();
    }
}
