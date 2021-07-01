<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class LogMysqlQueryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Event::listen(
            QueryExecuted::class,
            function (QueryExecuted $query): void {
                // Format binding data for sql insertion
                foreach ($query->bindings as $i => $binding) {
                    if ($binding instanceof \DateTime) {
                        $query->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                    } else {
                        if (is_string($binding)) {
                            $query->bindings[$i] = "'${binding}'";
                        }
                    }
                }
                // Insert bindings into query
                $boundSql = str_replace(['%', '?'], ['%%', '%s'], $query->sql);
                $boundSql = vsprintf($boundSql, $query->bindings);
                Log::channel('files')->debug(
                    "QUERY: ${boundSql};\n" .
                    "                                   TIME - {$query->time}ms"
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
