<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 只有当运行在 AWS 环境时才去拉取（本地开发时可以跳过，继续用本地 .env）
        if (app()->environment('production')) {

            // 1. 初始化 Secrets Manager 客户端 (自动读取机器的 LabRole 凭证)
            $client = new SecretsManagerClient([
                'region'  => env('AWS_DEFAULT_REGION'),
                'version' => 'latest'
            ]);

            $secretName = env('AWS_SECRET_NAME');

            try {
                // 2. 从云端获取密文
                $result = $client->getSecretValue([
                    'SecretId' => $secretName,
                ]);

                // 3. 解析 JSON
                $secret = json_decode($result['SecretString'], true);

                // 4. 关键：直接覆盖 Laravel 内存中的数据库配置项
                Config::set('database.connections.mysql.host', $secret['host']);
                Config::set('database.connections.mysql.username', $secret['username']);
                Config::set('database.connections.mysql.password', $secret['password']);
                Config::set('database.connections.mysql.database', $secret['dbInstanceIdentifier']);

            } catch (AwsException $e) {
                // 错误处理：如果获取失败，记录到 Laravel 日志中
                \Log::error("Secrets Manager 凭证获取失败: " . $e->getAwsErrorMessage());
            }
        }
    }
}
