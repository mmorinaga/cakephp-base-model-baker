<?php
namespace BaseModelBaker\Shell\Task;

use Cake\Console\Shell;
use Bake\Shell\Task\BakeTask;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class AppModelTask extends BakeTask
{
    public $pathFragment = 'Model/';

    public $tasks = [
        'Bake.BakeTemplate',
    ];

    public function main($name = null)
    {
        parent::main();
        $this->bake();
    }

    public function bake()
    {
        $this->bakeAppEntity();
        $this->bakeAppTable();
    }

    public function bakeAppEntity()
    {
        $this->out("\n" . 'Baking app_entity class...', 1, Shell::QUIET);
        $namespace = Configure::read('App.namespace');
        $this->BakeTemplate->set(compact('namespace'));
        $out = $this->BakeTemplate->generate('BaseModelBaker.Model/Entity/app_entity', compact('namespace'));
        $path = $this->getPath();
        $filename = $path . 'Entity' . DS . 'AppEntity.php';
        $this->createFile($filename, $out);

        $emptyFile = $path . 'Entity' . DS . 'empty';
        $this->_deleteEmptyFile($emptyFile);

        return $out;
    }

    public function bakeAppTable()
    {
        $this->out("\n" . 'Baking app_table class...', 1, Shell::QUIET);
        $namespace = Configure::read('App.namespace');
        $out = $this->BakeTemplate->generate('BaseModelBaker.Model/Table/app_table', compact('namespace'));
        $path = $this->getPath();
        $filename = $path . 'Table' . DS . 'AppTable.php';
        $this->createFile($filename, $out);

        // Work around composer caching that classes/files do not exist.
        // Check for the file as it might not exist in tests.
        if (file_exists($filename)) {
            require_once $filename;
        }
        TableRegistry::getTableLocator()->clear();

        $emptyFile = $path . 'Table' . DS . 'empty';
        $this->_deleteEmptyFile($emptyFile);

        return $out;
    }
}
