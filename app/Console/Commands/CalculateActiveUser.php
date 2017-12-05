<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CalculateActiveUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dream:calculate-active-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成活跃用户';

    public function handle(User $user)
    {
        $this->info('开始计算....');

        $user->calculateAndCacheActiveUsers();

        $this->info('计算成功！');
    }
}
