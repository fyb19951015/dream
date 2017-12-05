<?php

namespace App\Models\Traits;

use  App\Models\Topic;
use App\Models\Reply;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait ActiveUserHelper
{
    // 临时数据
    protected $users = [];

    protected $topic_weight = 4;// 话题权重
    protected $reply_weight = 1;// 回复权重
    protected $pass_days = 7;// 多少天内发表过内容
    protected $user_number = 6;// 取出多少用户

    // 缓存配置
    protected $cache_key = 'Dream_active_users';
    protected $cache_expire_in_minutes = 65;

    /**
     * 尝试从缓存中取出cache_key对应的数据，如果能取到，便直接返回数据
     * 否则通过匿名函数取出活跃用户数据，并存缓存
     *
     * @return mixed
     */
    public function getActiveUsers()
    {
        return Cache::remember($this->cache_key, $this->cache_expire_in_minutes, function () {
            return $this->calculateActiveUsers();
        });
    }

    public function calculateAndCacheActiveUsers()
    {
        // 取出活跃用户
        $active_users = $this->calculateActiveUsers();

        // 存缓存
        $this->cacheActiveUsers($active_users);
    }

    /**
     * 取活跃数据
     *
     * @return \Illuminate\Support\Collection
     */
    public function calculateActiveUsers()
    {
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        // 数组按照得分排序
        $users = array_sort($this->users, function ($user) {
            return $user['score'];
        });

        // 倒序，高分在前，第二个参数保持数组key不变
        $users = array_reverse($users, true);

        // 只获取想要的数量
        $users = array_slice($users, 0, $this->user_number, true);

        $active_users = collect();

        foreach ($users as $user_id => $user) {
            $user = $this->find($user_id);

            // 如果有数据
            if (count($user)) {
                // 将此用户实体放入临时数组的末尾
                $active_users->push($user);
            }
        }

        return $active_users;
    }

    /**
     * 从话题表里取出指定时间范围内，发表话题的用户，并且同时取出用户此段时间内发布话题的数量
     */
    private function calculateTopicScore()
    {
        $topic_users = Topic::query()->select(DB::raw('user_id, count(*) as topic_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')
            ->get();

        // 根据话题数量计分
        foreach ($topic_users as $value) {
            $this->users[$value->user_id]['score'] = $value->topic_count * $this->topic_weight;
        }
    }

    /**
     * 从回复表里取出指定时间内，回复的用户，并且取出用户在此段时间内回复的数量
     */
    private function calculateReplyScore()
    {
        $reply_users = Reply::query()->select(DB::raw('user_id, count(*) as reply_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')
            ->get();

        // 根据回复量计分
        foreach ($reply_users as $value) {
            $reply_score = $value->reply_count * $this->reply_weight;
            if (isset($this->users[$value->user_id])) {
                $this->users[$value->user_id]['score'] += $reply_score;
            } else {
                $this->users[$value->userid]['score'] = $reply_score;
            }
        }
    }

    private function cacheActiveUsers($active_users)
    {
        Cache::put($this->cache_key, $active_users, $this->cache_expire_in_minutes);
    }
}