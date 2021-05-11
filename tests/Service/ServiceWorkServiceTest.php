<?php

namespace App\Tests;

use App\Service\WorkService;
use PHPUnit\Framework\TestCase;

class ServiceWorkServiceTest extends TestCase
{
    private $workService;

    public function setUp()
    {
        $this->workService = $this->createMock(WorkService::class);
    }

    public function testGetEmployeeWorks()
    {
        $arr = [];
        $this->workService->expects($this->any())
            ->method('getEmployeeWorks')
            ->willReturn($arr)->withConsecutive(array('id' => 1));
        $this->assertSame($arr, $this->workService->getEmployeeWorks('1'));
    }

    public function testGetNodeEmployees()
    {
        $arr = [];
        $this->workService->expects($this->any())
            ->method('getNodeEmployees')
            ->willReturn($arr)->withConsecutive(array('nid' => 1));
        $this->assertSame($arr, $this->workService->getNodeEmployees('1'));
    }

    public function testFindSubnodes()
    {
        $arr = [];
        $this->workService->expects($this->any())
            ->method('findSubnodes')
            ->willReturn($arr)->withConsecutive(array('parent_id' => 1));
        $this->assertSame($arr, $this->workService->findSubnodes('1'));
    }

    public function testChangeWork()
    {
        $arr = [];
        $this->workService->expects($this->any())
            ->method('changeWork')
            ->willReturn($arr)->withConsecutive(array('nid' => 1, 'eid' => 1, 'rate' => 0.5));
        $this->assertSame($arr, $this->workService->changeWork(1, 1, 0.5));
    }

}
