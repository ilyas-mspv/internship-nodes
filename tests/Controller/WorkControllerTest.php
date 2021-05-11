<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WorkControllerTest extends WebTestCase
{
    public function testEmployeeNodes()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/works/employee/1');

        $this->assertResponseIsSuccessful();
    }

    public function testNodeEmployees()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/works/node/1');

        $this->assertResponseIsSuccessful();
    }

    public function testChangeWork()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/works', array('node_id' => '1', 'employee_id' => '1', 'rate' => '1'));

        $this->assertResponseIsSuccessful();
    }
}
