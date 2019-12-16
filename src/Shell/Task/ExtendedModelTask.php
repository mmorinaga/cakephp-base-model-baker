<?php
namespace BaseModelBaker\Shell\Task;

use Bake\Shell\Task\ModelTask;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ExtendedModelTask extends ModelTask
{

    public function bake($name)
    {
        $this->bakeExtendedEntity($name);
        $this->bakeExtendedTable($name);
    }

    public function bakeExtendedEntity($name)
    {
        $this->out("\n" . sprintf('Baking extended_entity class for %s...', $name), 1, Shell::QUIET);
        $name = $this->_entityName($name);
        $namespace = Configure::read('App.namespace');
        $out = $this->BakeTemplate->generate('BaseModelBaker.Model/Entity/extended_entity', compact('namespace', 'name'));
        $path = $this->getPath();
        $filename = $path . 'Entity' . DS . $name . '.php';
        $this->createFile($filename, $out);

        $emptyFile = $path . 'Entity' . DS . 'empty';
        $this->_deleteEmptyFile($emptyFile);

        return $out;
    }

    public function bakeExtendedTable($name)
    {
        $this->out("\n" . sprintf('Baking app_table class for %s...', $name), 1, Shell::QUIET);
        $namespace = Configure::read('App.namespace');
        $out = $this->BakeTemplate->generate('BaseModelBaker.Model/Table/extended_table', compact('namespace', 'name'));
        $path = $this->getPath();
        $filename = $path . 'Table' . DS . $name . 'Table.php';
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
