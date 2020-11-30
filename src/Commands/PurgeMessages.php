<?php

namespace RTippin\Messenger\Commands;

use Illuminate\Console\Command;
use RTippin\Messenger\Models\Message;

class PurgeMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messenger:purge:messages 
                                            {--now : Perform requested checks now instead of dispatching job}
                                            {--days=30 : Purge messages soft deleted X days ago or greater}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force delete regular messages (not images or documents) X days ago from soft delete';

    /**
     * Execute the console command. We will purge all soft deleted messages
     * that were archived past the set days. We do not need to fire any
     * additional events or load models into memory, just remove from
     * table, as this is not messages that are documents or images.
     *
     * @return void
     */
    public function handle(): void
    {
        $trashedMessages = Message::text()
            ->onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays($this->option('days')))
            ->forceDelete();

        $this->info("We purged {$trashedMessages} archived messages!");
    }
}
