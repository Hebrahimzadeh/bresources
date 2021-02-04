<?php

namespace Behamin\BResources\Tests;


class CreateResourcesTest extends TestCase
{
    protected $resourceName = 'TestResource';
    protected $resourceNamespace = 'App\Http\Resources\TestResource';

    protected $resourceCollectionName = 'TestResourceCollection';
    protected $resourceCollectionNamespace = 'App\Http\Resources\TestResourceCollection';

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->deleteResources();
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExistsAndValidateResourceClass(): void
    {
        \Artisan::call('make:bresource', [
            'name' => $this->resourceName,
        ]);
        $resourceFilePath = $this->resourceFilePath($this->resourceName);
        $this->existsAndValidateSimpleResources($resourceFilePath, $this->resourceNamespace);

        $this->deleteResources();
    }

    /**
     * test without use collectionResource Trait with get
     */
    public function testExistsAndValidateResourceCollectionClass():void
    {
        \Artisan::call('make:bcresource', [
            'name' => $this->resourceCollectionName
        ]);
        $resourceCollectionFilePath = $this->resourceFilePath($this->resourceCollectionName);
        $this->existsAndValidateSimpleResources($resourceCollectionFilePath, $this->resourceCollectionNamespace);

        $this->deleteResources();
    }

    protected function existsAndValidateSimpleResources($file, $namespace): void
    {
        //$namespace = 'asdf';
        $this->assertFileExists($file);

        $this->assertClassHasAttribute('data', $namespace);
        $this->assertClassHasAttribute('transform', $namespace);
        $this->assertTrue(method_exists($namespace, 'getArray'));
    }

    protected function resourceWithCollectionTest($testResource = true){
        if ($testResource) {
            $this->assertTrue(
                method_exists($this->resourceNamespace, 'getArray')
            );
        }

        if ($testResource) {
            $this->assertContains(
                'Behamin\BResources\Traits\CollectionResource',
                get_declared_traits()
            );
        }

        $collection = new $this->resourceCollectionNamespace(['data' => [], true]);
        $this->assertTrue(($collection instanceof $this->resourceCollectionNamespace));
    }

    public function testResourceWithCollection(){
        \Artisan::call('make:bresource', [
            'name' => $this->resourceName,
            '--collection' => true
            /*'--extends' =>*/
        ]);
        $this->resourceWithCollectionTest();

        $this->deleteResources();

        /**
         *
         */
        \Artisan::call('make:bresource', [
            'name' => $this->resourceCollectionName,
        ]);
        $this->resourceWithCollectionTest(false);

        $this->deleteResources();
    }

    protected function deleteResources()
    {
        if (file_exists($file = $this->resourceFilePath($this->resourceName))) {
            unlink($file);
        }
        if (file_exists(
            $file = $this->resourceFilePath($this->resourceCollectionName)
        )
        ) {
            unlink($file);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
        $this->deleteResources();
    }
}