<?php
/**
 * Created by PhpStorm.
 * User: paulo.bettini
 * Date: 2018-10-08
 * Time: 11:35
 */

namespace DataDictionaryBundle\Graph;

use Pimcore\Model\DataObject\ClassDefinition;
use DataDictionaryBundle\Graph\Entity\Node;
use DataDictionaryBundle\Graph\Visitor\FieldDefinition;

class Graph
{
    /**
     * Array of Nodes
     * @var Node[] $nodes
     */
    protected $nodes;

    /**
     * @return Node[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @param Node[] $nodes
     * @return Graph
     */
    public function setNodes(array $nodes): Graph
    {
        $this->nodes = $nodes;
        return $this;
    }

    /**
     * Graph constructor.
     */
    public function __construct()
    {
        foreach ($this->getClasses() as $class) {
            $this->nodes[$class] = Visitor\ClassDefinition::getNode(
                ClassDefinition::getByName($class)
            );
        }
        $this->addAttributes();
        $this->addRelations();
    }

    /**
     * Iterate over the nodes and create the attributes for them
     * @return $this
     * @throws \Exception
     */
    public function addAttributes()
    {
        foreach ($this->nodes as $node) {
            FieldDefinition::makeAttributes($node);
        }
        return $this;
    }

    /**
     * Iterate over the nodes and create the relations for them
     * @return $this
     * @throws \Exception
     */
    public function addRelations()
    {
        foreach ($this->nodes as $node) {
            FieldDefinition::makeRelationships($node);
        }
        return $this;
    }

    /**
     * @return mixed array of the classes name
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getClasses()
    {
        $classDefinition = new ClassDefinition();
        $db = $classDefinition->getDao()->db;
        return $db->fetchPairs('select * from classes order by 1 asc');
    }
}
