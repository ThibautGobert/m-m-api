<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class GenerateFrontEventsEnums extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:generate-front-enum {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->option('path');

        if (!$path) {
            $this->error('❌ Veuillez spécifier le chemin avec --path');
            return;
        }

        $eventsDir = app_path('Events');
        if (!is_dir($eventsDir)) {
            $this->error('❌ Le dossier app/Events n\'existe pas.');
            return;
        }

        $files = $this->getPhpFilesRecursive($eventsDir);
        $events = [];

        foreach ($files as $file) {
            $relativePath = str_replace($eventsDir . DIRECTORY_SEPARATOR, '', $file);
            $namespace = 'App\\Events\\' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['\\', ''], $relativePath);
            $baseName = basename($file, '.php');
            $subPath = trim(str_replace([$baseName . '.php', DIRECTORY_SEPARATOR], ['', ''], $relativePath), DIRECTORY_SEPARATOR);

            $key = $subPath ? str_replace(DIRECTORY_SEPARATOR, '', $subPath) . $baseName : $baseName;

            // Vérifie si broadcastAs() existe
            require_once $file;
            if (class_exists($namespace)) {
                $reflection = new ReflectionClass($namespace);
                if ($reflection->hasMethod('broadcastAs')) {
                    $method = $reflection->getMethod('broadcastAs');
                    if ($method->isPublic() && !$method->isStatic()) {
                        $instance = $reflection->newInstanceWithoutConstructor();
                        $asValue = $method->invoke($instance);
                        $events[$key] = $asValue;
                        continue;
                    }
                }
            }

            $events[$key] = str_replace('\\', '\\\\\\\\', $namespace);
        }

        $jsContent = "export const Events = {\n";
        foreach ($events as $key => $value) {
            $jsContent .= "    {$key}: '{$value}',\n";
        }
        $jsContent .= "}\n";

        $dir = dirname($path);

        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
            $this->info("✅ Dossier {$dir} créé.");
        }

        File::put($path, $jsContent);
        $this->info("✅ Fichier d'events généré avec succès dans {$path}");
    }

    private function getPhpFilesRecursive($dir): array
    {
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));

        $files = [];
        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }

            if ($file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }
}
