<?php
namespace BaseModelBaker\Shell\Task;

use Bake\Shell\Task\ModelTask;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class BaseModelTask extends ModelTask
{


    /**
     * Bake an base entity class.
     *
     * @param \Cake\ORM\Table $model Model name or object
     * @param array $data An array to use to generate the Table
     * @return string|null
     */
    public function bakeEntity($model, array $data = [])
    {
        if (!empty($this->params['no-entity'])) {
            return null;
        }
        $name = $this->_entityName($model->getAlias());

        $namespace = Configure::read('App.namespace');
        $pluginPath = '';
        if ($this->plugin) {
            $namespace = $this->_pluginNamespace($this->plugin);
            $pluginPath = $this->plugin . '.';
        }

        $data += [
            'name' => $name,
            'namespace' => $namespace,
            'plugin' => $this->plugin,
            'pluginPath' => $pluginPath,
            'primaryKey' => [],
        ];

        $this->BakeTemplate->set($data);
        $out = $this->BakeTemplate->generate('BaseModelBaker.Model/Entity/base_entity');

        $path = $this->getPath();
        $filename = $path . 'Entity' . DS . 'Base' . $name . '.php';
        $this->out("\n" . sprintf('Baking base_entity class for %s...', $name), 1, Shell::QUIET);
        $this->createFile($filename, $out);
        $emptyFile = $path . 'Entity' . DS . 'empty';
        $this->_deleteEmptyFile($emptyFile);

        return $out;
    }

    /**
     * Bake a base table class.
     *
     * @param \Cake\ORM\Table $model Model name or object
     * @param array $data An array to use to generate the Table
     * @return string|null
     */
    public function bakeTable($model, array $data = [])
    {
        if (!empty($this->params['no-table'])) {
            return null;
        }

        $namespace = Configure::read('App.namespace');
        $pluginPath = '';
        if ($this->plugin) {
            $namespace = $this->_pluginNamespace($this->plugin);
        }

        $name = $model->getAlias();
        $entity = $this->_entityName($model->getAlias());
        $data += [
            'plugin' => $this->plugin,
            'pluginPath' => $pluginPath,
            'namespace' => $namespace,
            'name' => $name,
            'entity' => $entity,
            'associations' => [],
            'primaryKey' => 'id',
            'displayField' => null,
            'table' => null,
            'validation' => [],
            'rulesChecker' => [],
            'behaviors' => [],
            'connection' => $this->connection,
        ];

        $this->BakeTemplate->set($data);
        $out = $this->BakeTemplate->generate('BaseModelBaker.Model/Table/base_table');

        $path = $this->getPath();
        $filename = $path . 'Table' . DS . 'Base' . $name . 'Table.php';
        $this->out("\n" . sprintf('Baking base_table class for %s...', $name), 1, Shell::QUIET);
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
