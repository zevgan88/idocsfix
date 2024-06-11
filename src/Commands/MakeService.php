<?php

namespace Werk365\IdentityDocuments\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeService extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'id:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new a OCR or FD service';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'ID Service';

    protected $stubName;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../stubs/'.$this->stubName;
    }

    public function handle()
    {
        $type = strtolower($this->argument('type'));
        switch ($type) {
            case 'ocr':
                $this->stubName = 'OcrServiceStub.php';
                break;
            case 'facedetection':
            case 'fd':
                $this->stubName = 'FaceDetectionServiceStub.php';
            break;
            case 'both':
                $this->stubName = 'OcrFdServiceStub.php';
                break;
            default:
                $this->error('Service Type not recognized, try using "OCR", "FaceDetection" or "Both"');

                return false;
        }
        // First we need to ensure that the given name is not a reserved word within the PHP
        // language and that the class name will actually be valid. If it is not valid we
        // can error now and prevent from polluting the filesystem using invalid files.
        if ($this->isReservedName($this->getNameInput())) {
            $this->error('The name "'.$this->getNameInput().'" is reserved by PHP.');

            return false;
        }

        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // Next, We will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((! $this->hasOption('force') ||
             ! $this->option('force')) &&
             $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name)));

        $this->info($this->type.' created successfully.');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'/Services';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the service'],
            ['type', InputArgument::REQUIRED, 'The type of the service, OCR - FaceDetection (fd) or Both'],
        ];
    }
}
