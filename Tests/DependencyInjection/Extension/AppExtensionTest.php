<?php

namespace Knp\Bundle\RadBundle\Tests\DependencyInjection\Extension;

class AppExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $xmlLoader;
    private $ymlLoader;

    protected function setUp()
    {
        $this->container = $this->getMockBuilder(
            'Symfony\Component\DependencyInjection\ContainerBuilder'
        )->disableOriginalConstructor()->getMock();

        $this->xmlLoader = $this->getMockBuilder(
            'Symfony\Component\DependencyInjection\Loader\XmlFileLoader'
        )->disableOriginalConstructor()->getMock();

        $this->ymlLoader = $this->getMockBuilder(
            'Symfony\Component\DependencyInjection\Loader\XmlFileLoader'
        )->disableOriginalConstructor()->getMock();
    }

    public function testLoadConfigs()
    {
        $extension = $this->createExtension(__DIR__);

        $this->container
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->with(
                $this->logicalOr($this->equalTo('app.key1'), $this->equalTo('app.key.number.2')),
                $this->logicalOr($this->equalTo('val1'), $this->equalTo('val2'))
            );

        $configs = array(
            array('key1' => 'val1'),
            array('key.number.2' => 'val2')
        );

        $this->xmlLoader
            ->expects($this->exactly(0))
            ->method('load');

        $this->ymlLoader
            ->expects($this->exactly(0))
            ->method('load');

        $extension->load($configs, $this->container);
    }

    public function testLoadSingleServiceResource()
    {
        $extension = $this->createExtension(__DIR__.'/fixtures/single');
        $configs   = array();

        $this->xmlLoader
            ->expects($this->once())
            ->method('load')
            ->with(__DIR__.'/fixtures/single/config/services.xml');

        $this->ymlLoader
            ->expects($this->once())
            ->method('load')
            ->with(__DIR__.'/fixtures/single/config/services.yml');

        $extension->load($configs, $this->container);
    }

    protected function createExtension($path)
    {
        $extension = $this->getMockBuilder(
            'Knp\Bundle\RadBundle\DependencyInjection\Extension\AppExtension'
        )
            ->setMethods(array('getXmlFileLoader', 'getYamlFileLoader'))
            ->setConstructorArgs(array($path))
            ->getMock();

        $extension
            ->expects($this->any())
            ->method('getXmlFileLoader')
            ->with($this->container)
            ->will($this->returnValue($this->xmlLoader));
        $extension
            ->expects($this->any())
            ->method('getYamlFileLoader')
            ->with($this->container)
            ->will($this->returnValue($this->ymlLoader));

        return $extension;
    }
}
