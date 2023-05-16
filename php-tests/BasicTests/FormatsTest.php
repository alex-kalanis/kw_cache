<?php

namespace BasicTests;


use CommonTestClass;
use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Format;
use kalanis\kw_cache\Simple\Variable;


class FormatsTest extends CommonTestClass
{
    /**
     * @throws CacheException
     */
    public function testInit(): void
    {
        $factory = new Format\Factory();
        $this->assertInstanceOf(Format\Format::class, $factory->getFormat(new Variable()));
    }

    /**
     * @throws CacheException
     */
    public function testRaw(): void
    {
        $format = new Format\Raw();
        $this->assertEquals('aaaaaaa', $format->unpack($format->pack('aaaaaaa')));
        $this->assertEquals('ear/a4vw-z.7v2!3#z', $format->unpack($format->pack('ear/a4vw-z.7v2!3#z')));
        $cont = $this->complicatedStructure(true);
        $this->assertEquals($cont, $format->unpack($format->pack($cont)));
    }

    /**
     * @throws CacheException
     */
    public function testSerialized(): void
    {
        $format = new Format\Serialized();
        $this->assertEquals('aaaaaaa', $format->unpack($format->pack('aaaaaaa')));
        $this->assertEquals('ear/a4vw-z.7v2!3#z', $format->unpack($format->pack('ear/a4vw-z.7v2!3#z')));
        $cont = $this->complicatedStructure(true);
        $this->assertEquals($cont, $format->unpack($format->pack($cont)));
    }

    /**
     * @throws CacheException
     */
    public function testFormat(): void
    {
        $format = new Format\Format();
        $this->assertEquals('aaaaaaa', $format->unpack($format->pack('aaaaaaa')));
        $this->assertEquals('ear/a4vw-z.7v2!3#z', $format->unpack($format->pack('ear/a4vw-z.7v2!3#z')));
        $this->assertEquals(false, $format->unpack($format->pack(false)));
        $this->assertEquals(1.75, $format->unpack($format->pack(1.75)));
        $cont = $this->complicatedStructure(false);
        $this->assertEquals($cont, $format->unpack($format->pack($cont)));
    }

    protected function complicatedStructure(bool $withObject = false)
    {
        $stdCl = new \stdClass();
        $stdCl->any = 'sdgghsdfh6976h4sd';
        $stdCl->sdg = 43.5424;
        $stdCl->ddd = new \stdClass();
        $stdCl->ddd->df56sh43 = 4351254;
        return ['6g8a7' => 'dfh4dg364sd6g', 'hzsdfgh' => 35.4534, 'sfkg' => false] + ($withObject ? ['hdhg' => $stdCl] : []);
    }
}
