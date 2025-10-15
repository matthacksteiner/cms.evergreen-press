<?php

use PHPUnit\Framework\TestCase;
use BaukastenApi\Api\IndexApi;
use BaukastenApi\Api\GlobalApi;

/**
 * API endpoint integration tests
 *
 * These tests verify that API classes exist and have the expected methods
 */
class ApiTest extends TestCase
{
    // IndexApi Tests

    public function testIndexApiClassExists()
    {
        $this->assertTrue(class_exists(IndexApi::class));
    }

    public function testIndexApiHasHandleMethod()
    {
        $this->assertTrue(method_exists(IndexApi::class, 'handle'));
    }

    public function testIndexApiHasGetDataMethod()
    {
        $this->assertTrue(method_exists(IndexApi::class, 'getData'));
    }

    public function testIndexApiHandleMethodIsStatic()
    {
        $reflection = new ReflectionMethod(IndexApi::class, 'handle');
        $this->assertTrue($reflection->isStatic());
    }

    public function testIndexApiGetDataMethodIsStatic()
    {
        $reflection = new ReflectionMethod(IndexApi::class, 'getData');
        $this->assertTrue($reflection->isStatic());
    }

    // GlobalApi Tests

    public function testGlobalApiClassExists()
    {
        $this->assertTrue(class_exists(GlobalApi::class));
    }

    public function testGlobalApiHasHandleMethod()
    {
        $this->assertTrue(method_exists(GlobalApi::class, 'handle'));
    }

    public function testGlobalApiHasGetDataMethod()
    {
        $this->assertTrue(method_exists(GlobalApi::class, 'getData'));
    }

    public function testGlobalApiHandleMethodIsStatic()
    {
        $reflection = new ReflectionMethod(GlobalApi::class, 'handle');
        $this->assertTrue($reflection->isStatic());
    }

    public function testGlobalApiGetDataMethodIsStatic()
    {
        $reflection = new ReflectionMethod(GlobalApi::class, 'getData');
        $this->assertTrue($reflection->isStatic());
    }

    // Test that API classes are in the correct namespace

    public function testIndexApiNamespace()
    {
        $reflection = new ReflectionClass(IndexApi::class);
        $this->assertEquals('BaukastenApi\Api', $reflection->getNamespaceName());
    }

    public function testGlobalApiNamespace()
    {
        $reflection = new ReflectionClass(GlobalApi::class);
        $this->assertEquals('BaukastenApi\Api', $reflection->getNamespaceName());
    }

    // Test method signatures

    public function testIndexApiHandleMethodSignature()
    {
        $reflection = new ReflectionMethod(IndexApi::class, 'handle');
        $this->assertEquals(0, $reflection->getNumberOfParameters());
    }

    public function testIndexApiGetDataMethodSignature()
    {
        $reflection = new ReflectionMethod(IndexApi::class, 'getData');
        $this->assertEquals(0, $reflection->getNumberOfParameters());
    }

    public function testGlobalApiHandleMethodSignature()
    {
        $reflection = new ReflectionMethod(GlobalApi::class, 'handle');
        $this->assertEquals(0, $reflection->getNumberOfParameters());
    }

    public function testGlobalApiGetDataMethodSignature()
    {
        $reflection = new ReflectionMethod(GlobalApi::class, 'getData');
        $this->assertEquals(0, $reflection->getNumberOfParameters());
    }

    // Test that methods are public

    public function testIndexApiMethodsArePublic()
    {
        $handleReflection = new ReflectionMethod(IndexApi::class, 'handle');
        $getDataReflection = new ReflectionMethod(IndexApi::class, 'getData');

        $this->assertTrue($handleReflection->isPublic());
        $this->assertTrue($getDataReflection->isPublic());
    }

    public function testGlobalApiMethodsArePublic()
    {
        $handleReflection = new ReflectionMethod(GlobalApi::class, 'handle');
        $getDataReflection = new ReflectionMethod(GlobalApi::class, 'getData');

        $this->assertTrue($handleReflection->isPublic());
        $this->assertTrue($getDataReflection->isPublic());
    }

    // Test class documentation

    public function testIndexApiHasDocComment()
    {
        $reflection = new ReflectionClass(IndexApi::class);
        $docComment = $reflection->getDocComment();

        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('IndexApi', $docComment);
        $this->assertStringContainsString('index.json', $docComment);
    }

    public function testGlobalApiHasDocComment()
    {
        $reflection = new ReflectionClass(GlobalApi::class);
        $docComment = $reflection->getDocComment();

        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('GlobalApi', $docComment);
        $this->assertStringContainsString('global.json', $docComment);
    }

    // Test method documentation

    public function testIndexApiMethodsHaveDocComments()
    {
        $handleReflection = new ReflectionMethod(IndexApi::class, 'handle');
        $getDataReflection = new ReflectionMethod(IndexApi::class, 'getData');

        $this->assertNotFalse($handleReflection->getDocComment());
        $this->assertNotFalse($getDataReflection->getDocComment());
    }

    public function testGlobalApiMethodsHaveDocComments()
    {
        $handleReflection = new ReflectionMethod(GlobalApi::class, 'handle');
        $getDataReflection = new ReflectionMethod(GlobalApi::class, 'getData');

        $this->assertNotFalse($handleReflection->getDocComment());
        $this->assertNotFalse($getDataReflection->getDocComment());
    }
}
