<?php

namespace Expresso\ExpressoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testConfigExpresso()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/configExpresso.php');

        $this->assertTrue($crawler->filter('html:contains("Expresso")')->count() > 0);
    }
}
