<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmployeeControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/employees');

        $this->assertResponseIsSuccessful();
    }

    public function testNewEmployee()
    {
        $client = static::createClient();
        $crawler = $client->request('POST',
            '/employee/new',
            array('name' => 'Ann'));

        $this->assertResponseIsSuccessful();
    }

    public function testGetOneEmployee()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/employee/'.'1');

        $this->assertResponseIsSuccessful();
    }

}
