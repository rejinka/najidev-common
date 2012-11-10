<?php

namespace NajiDev\Common\JavaScriptData;

use
	\ArrayObject,
	\PHPUnit_Framework_TestCase,
	\ReflectionClass
;

use \NajiDev\Common\Exception\InvalidArgumentException;


class ContainerTest extends PHPUnit_Framework_TestCase
{
	public function testAdd()
	{
		$instance = new Container();
		$instance->add('a', 'test');
		$expected = array(
			'a' => 'test'
		);

		$this->assertEquals($expected, $this->getData($instance));

		$instance = new Container();
		$instance->add('a.b', 'test');
		$expected = array(
			'a' => array(
				'b' => 'test'
			)
		);

		$this->assertEquals($expected, $this->getData($instance));
	}

	public function testDoubleAddWithSameKey()
	{
		$instance = new Container();
		$instance->add('a', 'old');
		$instance->add('a', 'new');
		$expected = array(
			'a' => 'old'
		);

		$this->assertEquals($expected, $this->getData($instance));

		$instance = new Container();
		$instance->add('a.b', 'old');
		$instance->add('a.b', 'new');
		$expected = array(
			'a' => array(
				'b' => 'old'
			)
		);

		$this->assertEquals($expected, $this->getData($instance));
	}

	public function testSet()
	{
		$instance = new Container();
		$instance->set('a', 'test');
		$expected = array(
			'a' => 'test'
		);

		$this->assertEquals($expected, $this->getData($instance));


		$instance = new Container();
		$instance->set('a.b', 'test');
		$expected = array(
			'a' => array(
				'b' => 'test'
			)
		);

		$this->assertEquals($expected, $this->getData($instance));

		// test with supported and unsupported data
		$instance = new Container();

		$instance->set('a', 'string');
		$instance->set('b', true);
		$instance->set('c', 12);
		$instance->set('d', 3.3);

		$expected = array(
			'a' => 'string',
			'b' => true,
			'c' => 12,
			'd' => 3.3
		);

		try
		{
			$instance->set('e', new ArrayObject());
			$this->fail('Exception not thrown');
		}
		catch (InvalidArgumentException $e) { }

		$this->assertEquals($expected, $this->getData($instance));
	}

	public function testDoubleSetWithSameKey()
	{
		$instance = new Container();
		$instance->set('a', 'old');
		$instance->set('a', 'new');
		$expected = array(
			'a' => 'new'
		);

		$this->assertEquals($expected, $this->getData($instance));

		$instance = new Container();
		$instance->set('a.b', 'old');
		$instance->set('a.b', 'new');
		$expected = array(
			'a' => array(
				'b' => 'new'
			)
		);

		$this->assertEquals($expected, $this->getData($instance));
	}

	public function testGet()
	{
		$instance = new Container();

		// test on empty container
		$this->assertEquals(null, $instance->get('a'));
		$this->assertEquals(null, $instance->get('a.b.c'));
		$this->assertEquals('blahblubb', $instance->get('a', 'blahblubb'));
		$this->assertEquals('blahblubb', $instance->get('a.b.c', 'blahblubb'));

		// test on non-empty container
		$this->setData($instance, array(
			'a' => 'test'
		));
		$this->assertEquals('test', $instance->get('a'));
		$this->assertEquals(null, $instance->get('b'));
		$this->assertEquals('test', $instance->get('a', 'blahblubb'));
		$this->assertEquals('blahblubb', $instance->get('b', 'blahblubb'));
	}

	public function testRemove()
	{
		$instance = new Container();

		// test on empty container
		$instance->remove('a');
		$this->assertEquals(array(), $this->getData($instance));

		$instance->remove('a.b');
		$this->assertEquals(array(), $this->getData($instance));

		// test on non-empty container
		$this->setData($instance, array(
			'a' => 'test'
		));
		$instance->remove('a');
		$this->assertEquals(array(), $this->getData($instance));

		$instance->remove('a.b');
		$this->assertEquals(array(), $this->getData($instance));

		// test on non-empty container (2)
		$this->setData($instance, array(
			'a' => array(
				'b' => 'test'
			)
		));
		$instance->remove('a.b');
		$this->assertEquals(array(), $this->getData($instance));

		// test on non-empty container (3)phpunit.xml.dist
		$this->setData($instance, array(
			'a' => array(
				'b' => 'test',
				'c' => 'test2'
			)
		));
		$instance->remove('a.b');
		$expected = array(
			'a' => array(
				'c' => 'test2'
			)
		);

		$this->assertEquals($expected, $this->getData($instance));
	}

	public function testGetData()
	{
		$instance = new Container();

		$expected = array(
			'a' => array(
				'b' => 'test',
				'c' => 'test2'
			)
		);

		$this->setData($instance, $expected);
		$this->assertEquals($expected, $instance->getData());
	}

	public function testGetTransformedData()
	{
		$container   = new Container();

		$this->setData($container, $data = array(
				'a' => 'string',
				'b' => true,
				'c' => 12,
				'd' => 3.3
			));

		$expected = json_encode($data);

		$this->assertEquals($expected, $container->getTransformedData());
	}

	protected function getReflectionProperty()
	{
		$class = new ReflectionClass('NajiDev\Common\JavaScriptData\Container');
		$property = $class->getProperty('data');
		$property->setAccessible(true);

		return $property;
	}

	protected function setData(Container $c, $data)
	{
		return $this->getReflectionProperty()->setValue($c, $data);
	}

	protected function getData(Container $c)
	{
		return $this->getReflectionProperty()->getValue($c);
	}
}