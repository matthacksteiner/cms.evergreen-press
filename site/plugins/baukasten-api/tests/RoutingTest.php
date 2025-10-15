<?php

use PHPUnit\Framework\TestCase;
use BaukastenApi\Api\Routes;
use BaukastenApi\Services\FlatUrlResolver;

/**
 * URL routing tests
 *
 * These tests verify that routing classes exist and have the expected structure
 */
class RoutingTest extends TestCase
{
    // Routes class tests

    public function testRoutesClassExists()
    {
        $this->assertTrue(class_exists(Routes::class));
    }

    public function testRoutesHasGetRoutesMethod()
    {
        $this->assertTrue(method_exists(Routes::class, 'getRoutes'));
    }

    public function testRoutesGetRoutesMethodIsStatic()
    {
        $reflection = new ReflectionMethod(Routes::class, 'getRoutes');
        $this->assertTrue($reflection->isStatic());
    }

    public function testRoutesGetRoutesMethodIsPublic()
    {
        $reflection = new ReflectionMethod(Routes::class, 'getRoutes');
        $this->assertTrue($reflection->isPublic());
    }

    public function testRoutesGetRoutesReturnsArray()
    {
        $routes = Routes::getRoutes();
        $this->assertIsArray($routes);
    }

    public function testRoutesContainsExpectedRoutes()
    {
        $routes = Routes::getRoutes();
        $this->assertNotEmpty($routes);

        // Check that we have the expected number of routes
        $this->assertGreaterThanOrEqual(4, count($routes));

        // Check for specific route patterns
        $patterns = array_column($routes, 'pattern');
        $this->assertContains('index.json', $patterns);
        $this->assertContains('global.json', $patterns);
        $this->assertContains('/', $patterns);
        $this->assertContains('(:any).json', $patterns);
        $this->assertContains('(:any)', $patterns);
    }

    public function testRoutesHaveRequiredStructure()
    {
        $routes = Routes::getRoutes();

        foreach ($routes as $route) {
            $this->assertIsArray($route);
            $this->assertArrayHasKey('pattern', $route);
            $this->assertArrayHasKey('method', $route);
            $this->assertArrayHasKey('action', $route);

            // Check that action is callable
            $this->assertTrue(is_callable($route['action']));
        }
    }

    public function testIndexJsonRouteStructure()
    {
        $routes = Routes::getRoutes();
        $indexRoute = null;

        foreach ($routes as $route) {
            if ($route['pattern'] === 'index.json') {
                $indexRoute = $route;
                break;
            }
        }

        $this->assertNotNull($indexRoute);
        $this->assertEquals('*', $indexRoute['language']);
        $this->assertEquals('GET', $indexRoute['method']);
        $this->assertTrue(is_callable($indexRoute['action']));
    }

    public function testGlobalJsonRouteStructure()
    {
        $routes = Routes::getRoutes();
        $globalRoute = null;

        foreach ($routes as $route) {
            if ($route['pattern'] === 'global.json') {
                $globalRoute = $route;
                break;
            }
        }

        $this->assertNotNull($globalRoute);
        $this->assertEquals('*', $globalRoute['language']);
        $this->assertEquals('GET', $globalRoute['method']);
        $this->assertTrue(is_callable($globalRoute['action']));
    }

    public function testRootRouteStructure()
    {
        $routes = Routes::getRoutes();
        $rootRoute = null;

        foreach ($routes as $route) {
            if ($route['pattern'] === '/') {
                $rootRoute = $route;
                break;
            }
        }

        $this->assertNotNull($rootRoute);
        $this->assertEquals('GET', $rootRoute['method']);
        $this->assertTrue(is_callable($rootRoute['action']));
    }

    // FlatUrlResolver class tests

    public function testFlatUrlResolverClassExists()
    {
        $this->assertTrue(class_exists(FlatUrlResolver::class));
    }

    public function testFlatUrlResolverHasExpectedMethods()
    {
        $expectedMethods = [
            'handleFlatUrlResolution',
            'findPagesByFlatUrl',
            'resolvePriorityConflict',
            'tryHierarchicalFallback',
            'isSectionPageAccessible'
        ];

        foreach ($expectedMethods as $method) {
            $this->assertTrue(method_exists(FlatUrlResolver::class, $method));
        }
    }

    public function testFlatUrlResolverMethodsAreStatic()
    {
        $methods = [
            'handleFlatUrlResolution',
            'findPagesByFlatUrl',
            'resolvePriorityConflict',
            'tryHierarchicalFallback',
            'isSectionPageAccessible'
        ];

        foreach ($methods as $method) {
            $reflection = new ReflectionMethod(FlatUrlResolver::class, $method);
            $this->assertTrue($reflection->isStatic(), "Method $method should be static");
        }
    }

    public function testFlatUrlResolverMethodsArePublic()
    {
        $methods = [
            'handleFlatUrlResolution',
            'findPagesByFlatUrl',
            'resolvePriorityConflict',
            'tryHierarchicalFallback',
            'isSectionPageAccessible'
        ];

        foreach ($methods as $method) {
            $reflection = new ReflectionMethod(FlatUrlResolver::class, $method);
            $this->assertTrue($reflection->isPublic(), "Method $method should be public");
        }
    }

    public function testFlatUrlResolverNamespace()
    {
        $reflection = new ReflectionClass(FlatUrlResolver::class);
        $this->assertEquals('BaukastenApi\Services', $reflection->getNamespaceName());
    }

    public function testFlatUrlResolverHasDocComment()
    {
        $reflection = new ReflectionClass(FlatUrlResolver::class);
        $docComment = $reflection->getDocComment();

        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('Service for handling', $docComment);
        $this->assertStringContainsString('flat URL resolution', $docComment);
    }

    public function testFlatUrlResolverMethodSignatures()
    {
        // Test handleFlatUrlResolution method signature
        $reflection = new ReflectionMethod(FlatUrlResolver::class, 'handleFlatUrlResolution');
        $this->assertEquals(1, $reflection->getNumberOfParameters());

        $params = $reflection->getParameters();
        $this->assertEquals('path', $params[0]->getName());

        // Test findPagesByFlatUrl method signature
        $reflection = new ReflectionMethod(FlatUrlResolver::class, 'findPagesByFlatUrl');
        $this->assertEquals(2, $reflection->getNumberOfParameters());

        $params = $reflection->getParameters();
        $this->assertEquals('path', $params[0]->getName());
        $this->assertEquals('segments', $params[1]->getName());

        // Test resolvePriorityConflict method signature
        $reflection = new ReflectionMethod(FlatUrlResolver::class, 'resolvePriorityConflict');
        $this->assertEquals(2, $reflection->getNumberOfParameters());

        $params = $reflection->getParameters();
        $this->assertEquals('candidates', $params[0]->getName());
        $this->assertEquals('path', $params[1]->getName());

        // Test tryHierarchicalFallback method signature
        $reflection = new ReflectionMethod(FlatUrlResolver::class, 'tryHierarchicalFallback');
        $this->assertEquals(2, $reflection->getNumberOfParameters());

        $params = $reflection->getParameters();
        $this->assertEquals('path', $params[0]->getName());
        $this->assertEquals('segments', $params[1]->getName());

        // Test isSectionPageAccessible method signature
        $reflection = new ReflectionMethod(FlatUrlResolver::class, 'isSectionPageAccessible');
        $this->assertEquals(1, $reflection->getNumberOfParameters());

        $params = $reflection->getParameters();
        $this->assertEquals('page', $params[0]->getName());
    }

    public function testRoutesNamespace()
    {
        $reflection = new ReflectionClass(Routes::class);
        $this->assertEquals('BaukastenApi\Api', $reflection->getNamespaceName());
    }

    public function testRoutesHasDocComment()
    {
        $reflection = new ReflectionClass(Routes::class);
        $docComment = $reflection->getDocComment();

        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('Routes', $docComment);
    }

    public function testRoutesGetRoutesMethodHasDocComment()
    {
        $reflection = new ReflectionMethod(Routes::class, 'getRoutes');
        $docComment = $reflection->getDocComment();

        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('Get all routes', $docComment);
    }

    public function testFlatUrlResolverMethodsHaveDocComments()
    {
        $methods = [
            'handleFlatUrlResolution',
            'findPagesByFlatUrl',
            'resolvePriorityConflict',
            'tryHierarchicalFallback',
            'isSectionPageAccessible'
        ];

        foreach ($methods as $method) {
            $reflection = new ReflectionMethod(FlatUrlResolver::class, $method);
            $docComment = $reflection->getDocComment();
            $this->assertNotFalse($docComment, "Method $method should have a doc comment");
        }
    }
}
