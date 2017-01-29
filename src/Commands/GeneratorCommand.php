<?php

namespace QuickGen\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use QuickGen\Compilers\StubCompiler;
use QuickGen\Variable;

/**
 * Class GeneratorCommand
 *
 * @package QuickGen\Commands
 */
class GeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quick-gen:generate {name} {--template=crud}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a basic crud.';

    /**
     * Config array
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var Filesystem
     */
    protected $filesystem;


    /**
     * GeneratorCommand constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting Generator');

        $this->checkFilesHaveBeenPublished();

        // Setup default config array
        $this->config = config('quick-gen');
        $this->config = array_merge($this->config, [
            'resource_name' => str_singular($this->argument('name')),
            'stub_path'     => rtrim($this->config['stub_path'], '/') . '/' . $this->option('template') . '/'
        ]);

        // Check the specified stub exists
        if (!\File::isDirectory($this->config['stub_path'])) {
            $this->error("No stub structure with the name '{$this->option('template')}' found.");
            return;
        }

        // Run the config options past the user to allow them to make any adjustments required/
        if ($this->confirmConfig() === false) {
            return;
        }

        // Setup filesystem
        $this->setAdapterBasePath($this->config['stub_path']);

        // Configure variables that are available within stubs
        $variables = [
            'name' => array_get($this->config, 'resource_name'),
            'base_namespace' => array_get($this->config, 'base_namespace')// . '\\' . studly_case(array_get($this->config, 'resource_name'))
        ];

        // Map variables to Variable objects
        $variables = array_map(function ($variable) {
            return new Variable($variable);
        }, $variables);

        $files = $this->filesystem->allFiles();

        $progress = $this->output->createProgressBar(count($files));

        foreach ($this->filesystem->allFiles() as $file) {
            $compiler = new StubCompiler($file, $variables, $this->config);

            $baseOutputDir = base_path(); //$this->config['base_namespace_path'];

            $outputFile = $compiler->compileFilename();

            foreach ($this->config['output_directories'] as $match => $path) {
                if (starts_with($file, $match)) {
                    $outputFile = str_replace($match, '', $outputFile);
                    $baseOutputDir = $path;

                    break;
                }
            }

            $this->setAdapterBasePath($baseOutputDir);

            $this->filesystem->put(
                $outputFile,
                $compiler->compileContent()
            );

            // Advance the progress bar
            $progress->advance();
        }

        $progress->finish();
        $this->info("\n");
        $this->info("Generator complete.");
    }

    protected function confirmConfig()
    {
        $this->config['resource_name'] = $this->ask('Singular resource name', $this->config['resource_name']);
        $this->config['base_namespace'] = $this->ask('Base namespace to generate into', $this->config['base_namespace']);

        $rows = [
            ['Template', $this->option('template')]
        ];

        foreach (array_except($this->config, ['output_directories', 'stub_path']) as $key => $value) {
            $rows[] = [
                ucwords(str_replace('_', ' ', $key)),
                $value
            ];
        }

        $this->table(
            ['Option', 'Value'],
            $rows
        );

        if (!$this->confirm('Please confirm the above options are correct.', true)) {
            $this->info('Please confirm the following options.');
            $this->confirmConfig();
        }
    }

    /**
     * Set the filesystem adapter basepath
     *
     * @param $path
     */
    private function setAdapterBasePath($path)
    {
        $this->filesystem->getDriver()->getAdapter()->setPathPrefix($path);
    }

    /**
     * Check Check config/stubs
     */
    private function checkFilesHaveBeenPublished()
    {
        if (!is_array(config('quick-gen'))) {
            throw new \RuntimeException("Please run [php artisan vendor:publish --tag=config] to publish the config files.");
        }

        if (!\File::isDirectory(config('quick-gen.stub_path'))) {
            throw new \RuntimeException("Please run [php artisan vendor:publish --tag=stubs] to publish the stub files.");
        }
    }
}
