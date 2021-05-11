<?php

namespace App\Tests;

use App\Entity\Employee;
use App\Service\EmployeeService;
use PHPUnit\Framework\TestCase;

class ServiceEmployeeServiceTest extends TestCase
{
    private $employeeService;

    public function setUp()
    {
        $this->employeeService = $this->createMock(EmployeeService::class);
    }

    public function testGetEmployees()
    {
        $arr = [];
        $this->employeeService->expects($this->any())
            ->method('getEmployees')
            ->willReturn($arr);
        $this->assertSame($arr, $this->employeeService->getEmployees());
    }

    public function testGetOneEmployee()
    {
        $dto = [];
        $this->employeeService->expects($this->any())
            ->method('getOneEmployee')
            ->willReturn($dto)->withConsecutive(array('id'=>2));
        $this->assertSame($dto, $this->employeeService->getOneEmployee('2'));
    }

    public function testNewEmployee()
    {
        $employee = new Employee();
        $this->employeeService->expects($this->any())
            ->method('getOneEmployee')
            ->willReturn($employee)->withConsecutive(array('name'=>'Ann'));

        $this->assertInstanceOf('App\Entity\Employee', $this->employeeService->getOneEmployee('Ann'));
    }
}
