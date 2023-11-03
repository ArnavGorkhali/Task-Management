<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\EventFunction;
use App\Models\Task;
use Illuminate\Console\Command;

class ChangePriorityStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'priority:change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Events');
        Event::withoutGlobalScopes()->whereIn('priority', ['1', '0'])->update(['priority'=>'low']);
        $this->info('Functions');
        EventFunction::withoutGlobalScopes()->whereIn('priority', ['1', '0'])->update(['priority'=>'low']);
        $this->info('Tasks');
        Task::withoutGlobalScopes()->whereIn('priority', ['1', '0'])->update(['priority'=>'low']);
        $this->info('Done');
    }
}
