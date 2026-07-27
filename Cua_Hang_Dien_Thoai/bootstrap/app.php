<?php

use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler;

 $app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

 $app->singleton(
    HttpKernelContract::class,
    App\Http\Kernel::class
);

 $app->singleton(
    ConsoleKernelContract::class,
    App\Console\Kernel::class
);

 $app->singleton(
    ExceptionHandler::class,
    App\Exceptions\Handler::class
);

return $app;