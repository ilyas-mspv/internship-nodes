<?php

namespace App\Tests;

use App\Service\NodesService;
use PHPUnit\Framework\TestCase;

class ServiceNodesServiceTest extends TestCase
{
    private $nodesService;

    public function setUp()
    {
        $this->nodesService = $this->createMock(NodesService::class);
    }

    public function testGetNode()
    {
        $arr = [];
        $this->nodesService->expects($this->any())
            ->method('getNode')
            ->willReturn($arr)->withConsecutive(array('id' => 1));
        $this->assertSame($arr, $this->nodesService->getNode('1'));
    }

    public function testGetOneNode()
    {
        $arr = [];
        $this->nodesService->expects($this->any())
            ->method('getOneNode')
            ->willReturn($arr)->withConsecutive(array('id' => 1));
        $this->assertSame($arr, $this->nodesService->getOneNode('1'));
    }

    public function testShowAll()
    {
        $arr = ['Ann'];
        $this->nodesService->expects($this->any())
            ->method('showAll')
            ->willReturn($arr);
        $this->assertNotEmpty($this->nodesService->showAll());
    }

    public function testShow()
    {
        $arr = [];
        $this->nodesService->expects($this->any())
            ->method('show')
            ->willReturn($arr)->withConsecutive(array('output' => 'pdf'));
        $this->assertSame($arr, $this->nodesService->show('pdf'));
    }

    public function testUpdateNode()
    {
        $arr = [];
        $this->nodesService->expects($this->any())
            ->method('updateNode')
            ->willReturn($arr)->withConsecutive(array('id' => 1, 'parent_id' => 1, 'name' => 'Ann'));
        $this->assertSame($arr, $this->nodesService->updateNode(1, 1, 'Ann'));
    }

    public function testNewNode()
    {
        $arr = [];
        $this->nodesService->expects($this->any())
            ->method('newNode')
            ->willReturn($arr)->withConsecutive(array('name' => 'Ann', 'parent_id' => 1));
        $this->assertSame($arr, $this->nodesService->newNode('Ann', 1));
    }

    public function testRemoveNode()
    {
        $arr = ['ok'=>true];
        $this->nodesService->expects($this->any())
            ->method('removeNode')
            ->willReturn($arr)->withConsecutive(array('id' => 2));
        $this->assertEquals($arr, $this->nodesService->removeNode('2'));
    }
}
