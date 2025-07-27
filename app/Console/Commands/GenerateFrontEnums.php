<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionEnum;
use BackedEnum;

class GenerateFrontEnums extends Command
{
    protected $signature = 'enum:export-all {--path=}';
    protected $description = 'Exporte tous les enums PHP de App\\Enums vers des fichiers JS';

    public function handle(): void
    {
        $targetPath = $this->option('path');

        if (!$targetPath) {
            $this->error('❌ Merci de spécifier le chemin de sortie avec --path');
            return;
        }

        $enumDir = app_path('Enums');
        if (!is_dir($enumDir)) {
            $this->error("❌ Le dossier {$enumDir} n'existe pas.");
            return;
        }

        $files = $this->getPhpFilesRecursive($enumDir);

        foreach ($files as $file) {
            require_once $file;

            $relativePath = str_replace($enumDir . DIRECTORY_SEPARATOR, '', $file);
            $namespace = 'App\\Enums\\' . str_replace(['/', '\\', '.php'], ['\\', '\\', ''], $relativePath);
            $className = class_basename($namespace);

            if (!enum_exists($namespace)) {
                $this->warn("⏭️  $namespace ignoré (pas un enum)");
                continue;
            }

            $reflection = new ReflectionEnum($namespace);
            $isBacked = is_subclass_of($namespace, BackedEnum::class);
            $constants = [];

            foreach ($reflection->getCases() as $case) {
                $enumCase = $case->getValue(); // Ex: ConversationType::PRIVATE
                $value = $isBacked ? $enumCase->value : $enumCase->name;
                $constants[$enumCase->name] = $value;
            }

            $js = "export const {$className} = {\n";
            foreach ($constants as $key => $value) {
                if (is_numeric($value)) {
                    $js .= "  {$key}: {$value},\n"; // pas de guillemets
                } else {
                    $escaped = addslashes($value);
                    $js .= "  {$key}: '{$escaped}',\n";
                }
            }
            $js .= "};\n";

            $filePath = rtrim($targetPath, '/') . "/{$className}.js";

            File::ensureDirectoryExists(dirname($filePath));
            File::put($filePath, $js);

            $this->info("✅ {$className}.js généré");
        }

        $this->info("🎉 Tous les enums ont été exportés avec succès !");
    }

    private function getPhpFilesRecursive(string $dir): array
    {
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        $files = [];

        foreach ($rii as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
