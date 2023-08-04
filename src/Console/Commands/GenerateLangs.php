<?php /** @noinspection PhpIllegalPsrClassPathInspection */

namespace App\Console\Commands;

use HichemtabTech\LangifyLaravel\LangGeneratingType;
use HichemtabTech\LangifyLaravel\Langify;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateLangs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'langs:generate {origin} {--overwrite=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $origine = $this->argument('origin');
        $type = $this->option('overwrite') == 'null' ? LangGeneratingType::COMPLETE_THE_MISSING : LangGeneratingType::FORCE_OVERWRITE;

        $defaultLangs = [];
        $defaultLangs = array_map(function ($x) {
            return basename($x);
        }, File::directories(lang_path()));
        if (in_array($origine, $defaultLangs)) {
            $defaultLangs = array_filter($defaultLangs, function ($x) use ($origine) {
                return $x != $origine;
            });
        } else {
            $this->error("The origin language does not exist");
            return;
        }

        $langs = $this->ask("Which languages do you want to generate? (comma separated)", implode(',', $defaultLangs));

        $langs = explode(',', $langs);
        $langs = array_map(function ($x) {
            return trim($x);
        }, $langs);

        $existedData = [];
        if ($type == LangGeneratingType::COMPLETE_THE_MISSING) {
            foreach ($langs as $lang) {
                $existedData[$lang] = [];
                if (!File::exists(lang_path($lang))) {
                    continue;
                }
                $files = File::files(lang_path($lang));
                foreach ($files as $file) {
                    $existedData[$lang][basename($file, '.php')] = require $file;
                }
            }
        }

        $defaultData = [];
        $files = File::files(lang_path($origine));
        foreach ($files as $file) {
            $defaultData[basename($file, '.php')] = require $file;
        }


        $langify = new Langify();
        $langify->setGeneratingMode($type);
        $langify->setLanguages($langs);
        $langify->setDefaultLanguage($origine);
        $langify->setExistedData($existedData);
        $langify->setDefaultData($defaultData);

        $progressBar = null;

        $this->info("Generating languages...");

        $langify->setInitProgress(function ($all) use(&$progressBar) {
            $progressBar = $this->output->createProgressBar($all);
            $progressBar->setProgress(0);
        });
        $langify->setProgress(function ($all, $done) use(&$progressBar) {
            $progressBar->setProgress($done);
        });
        $langify->generate();

        $result = $langify->getResults();

        //refill the files with results using stub langPlaceholder.stub.stub by creating a file named the first level key of the array and puts inside the values under
        foreach ($result as $lang => $data) {
            foreach ($data as $key => $value) {
                $replacements = [
                    '{{CONTENT}}' => $this->var_export_with_square_brackets(var_export($value, true)),
                ];
                $template = File::get(__DIR__.'/stubs/langPlaceholder.stub');
                $generatedClass = str_replace(array_keys($replacements), array_values($replacements), $template);

                $classPath = lang_path($lang.'/'.$key.'.php');
                File::ensureDirectoryExists(dirname($classPath));
                File::put($classPath, $generatedClass);
            }
        }
        $this->info("Done!");
    }

    function var_export_with_square_brackets($expression): array|string|null
    {
        return str_replace(["array (\n", "\n)"], ["[\n", "\n];"], $expression);
    }
}