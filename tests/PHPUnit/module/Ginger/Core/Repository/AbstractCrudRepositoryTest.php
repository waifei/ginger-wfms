<?php

namespace Ginger\Core\Repository;

use Ginger\Test\TestCase;
use Ginger\Core\Repository\Resource;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-11-16 at 23:25:35.
 */
class AbstractCrudRepositoryTest extends TestCase
{

    /**
     * @var AbstractCrudRepository
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $resourceType = new Resource\ResourceType('workflow');
        $adapter = new Adapter\ZendDbCrudRepositoryAdapter($this->initCrudDatabase());
        $this->object = new AbstractCrudRepository($resourceType, $adapter);
    }

    /**
     * @covers Ginger\Core\Repository\AbstractCrudRepository::create
     */
    public function testCreate()
    {
        $resourceData = new Resource\ResourceData();
        $resourceData->setData(array('name' => 'DataTransfer'));
        
        $resourceId = $this->object->create($resourceData);
        
        $this->assertInstanceOf('Ginger\Core\Repository\Resource\ResourceId', $resourceId);
        $this->assertEquals(1, $resourceId->getValue());
    }

    /**
     * @covers Ginger\Core\Repository\AbstractCrudRepository::delete
     */
    public function testDelete()
    {
        $resourceData = new Resource\ResourceData();
        $resourceData->setData(array('name' => 'DataTransfer'));
        
        $resourceId = $this->object->create($resourceData);
        
        $this->object->delete($resourceId);
        
        //If no exception was thrown, deletion of resource was successful
        $this->assertTrue(true);
    }

    /**
     * @covers Ginger\Core\Repository\AbstractCrudRepository::update
     */
    public function testUpdate()
    {
        $resourceData = new Resource\ResourceData();
        $resourceData->setData(array('name' => 'DataTransfer', 'desc' => 'simple data transfer'));
        
        $resourceId = $this->object->create($resourceData);
        
        $resourceData = new Resource\ResourceData($resourceId);
        $resourceData->setData(array('name' => 'FileUpload'));
        
        $resourceData = $this->object->update($resourceData);
        
        $this->assertInstanceOf('Ginger\Core\Repository\Resource\ResourceData', $resourceData);
        $this->assertEquals($resourceId->getValue(), $resourceData->getResourceId()->getValue());
        $this->assertEquals(
            array(
                'name' => 'FileUpload', 
                'desc' => 'simple data transfer'
            ), 
            $resourceData->getData()
        );
    }

    /**
     * @covers Ginger\Core\Repository\AbstractCrudRepository::read
     */
    public function testRead()
    {
        $resourceData = new Resource\ResourceData();
        $resourceData->setData(array('name' => 'DataTransfer'));
        
        $resourceId = $this->object->create($resourceData);
        
        $resourceData = $this->object->read($resourceId);
        
        $this->assertInstanceOf('Ginger\Core\Repository\Resource\ResourceData', $resourceData);
        $this->assertEquals($resourceId->getValue(), $resourceData->getResourceId()->getValue());
        $this->assertEquals(array('name' => 'DataTransfer'), $resourceData->getData());
    }

    /**
     * @covers Ginger\Core\Repository\AbstractCrudRepository::listAll
     */
    public function testListAll()
    {
        $resourceData = new Resource\ResourceData();
        $resourceData->setData(array('name' => 'DataTransfer'));
        
        $resourceId1 = $this->object->create($resourceData);
        
        $resourceData = new Resource\ResourceData();
        $resourceData->setData(array('name' => 'FileUpload'));
        
        $resourceId2 = $this->object->create($resourceData);
        
        $resources = $this->object->listAll();
        
        $resourceData1 = $resources[0];
        
        $this->assertInstanceOf('Ginger\Core\Repository\Resource\ResourceData', $resourceData1);
        $this->assertEquals($resourceId1->getValue(), $resourceData1->getResourceId()->getValue());
        $this->assertEquals(array('name' => 'DataTransfer'), $resourceData1->getData());
        
        $resourceData2 = $resources[1];
        
        $this->assertInstanceOf('Ginger\Core\Repository\Resource\ResourceData', $resourceData2);
        $this->assertEquals($resourceId2->getValue(), $resourceData2->getResourceId()->getValue());
        $this->assertEquals(array('name' => 'FileUpload'), $resourceData2->getData());
    }

}