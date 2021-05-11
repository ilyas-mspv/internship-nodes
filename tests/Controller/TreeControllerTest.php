<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TreeControllerTest extends WebTestCase
{
    public function testAll()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tree/plain');

        $this->assertResponseIsSuccessful();
    }

    public function testAll_json()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tree/plain/json');

        $this->assertResponseIsSuccessful();
    }

    public function testAll_pdf()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tree/plain/pdf');

        $this->assertResponseIsSuccessful();
    }

    public function testAll_excel()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tree/plain/excel');

        $this->assertResponseIsSuccessful();
    }

    public function testTreeView()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tree/all');

        $this->assertResponseIsSuccessful();
    }

    public function testNewNode()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/node/new', array('name'=> 'Ann', 'parent_id'=> 1));

        $this->assertResponseIsSuccessful();
    }

    public function testGetNode()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/node/'. '1');

        $this->assertResponseIsSuccessful();
    }

    public function testUpdateNode()
    {
        $client = static::createClient();
        $crawler = $client->request('PUT', '/node/' . '1', array('parent_id'=> '1', 'name' => 'Ann'));

        $this->assertResponseIsSuccessful();
    }

    public function testDeleteNode()
    {
        $client = static::createClient();
        $crawler = $client->request('DELETE', '/node/1');

        $this->assertResponseIsSuccessful();
    }
}
