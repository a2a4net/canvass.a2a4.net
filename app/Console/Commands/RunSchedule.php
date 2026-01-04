<?php

namespace App\Console\Commands;

class RunSchedule extends MakeSchedule
{
    protected $signature = 'app:RunSchedule';
    protected $description = 'Симуляція обходу. Працює в будні';

    public function handle()
    {
        $this->handleTasks();
    }
}
