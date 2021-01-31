<?php

namespace Behamin\BResources\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeBResourceCollectionCommand extends GeneratorCommand
{
    protected $name = 'make:bcresource';
    protected $type = 'BResourceCollection';
    protected $extendsClass = 'BasicResourceCollection';
    protected $use = 'use Behamin\BResources\Resources\BasicResourceCollection;';
    protected $useTrait = 'use CollectionResource;';

    protected function getStub()
    {
        return __DIR__ . '/stubs/resource-collection.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Resources';
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the resource collection class.'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['extends', 'ex', InputOption::VALUE_NONE, 'extends from custom basic resource'],
        ];
    }

    protected function buildClass($name)
    {
        return $this->replaceExtends(parent::buildClass($name));
    }

    /**
     * @param $stub
     * @return string|string[]
     */
    public function replaceExtends($stub){

        if ($this->option('extends') && is_string($this->option('extends'))) {
            $this->extendsClass = $this->option('extends');
            $this->use = 'use Behamin\BResources\Traits\CollectionResource;';
        } else {
            $this->useTrait = <<<EOF
    public function __construct(\$resourceCollection)
    {
        parent::__construct(\$resourceCollection, false);
    }

    public function getArray(\$resource)
    {
        return [
            //
        ];
    }
EOF;
        }

        $stub = str_replace(['{{ extends }}', '{{extends}}'], $this->extendsClass, $stub);
        $stub = str_replace(['{{ useTrait }}', '{{useTrait}}'], $this->useTrait, $stub);
        return str_replace(['{{ use }}', '{{use}}'], $this->use, $stub);
    }
}