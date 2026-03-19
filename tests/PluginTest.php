<?php

declare(strict_types=1);

namespace Detain\MyAdminNovnc\Tests;

use Detain\MyAdminNovnc\Plugin;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class PluginTest
 *
 * Unit tests for the Detain\MyAdminNovnc\Plugin class.
 *
 * @package Detain\MyAdminNovnc\Tests
 */
class PluginTest extends TestCase
{
    /**
     * Test that the Plugin class exists and can be loaded.
     *
     * @return void
     */
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Plugin::class));
    }

    /**
     * Test that the Plugin class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated(): void
    {
        $plugin = new Plugin();
        $this->assertInstanceOf(Plugin::class, $plugin);
    }

    /**
     * Test that the $name static property is set correctly.
     *
     * @return void
     */
    public function testNameProperty(): void
    {
        $this->assertSame('Novnc Plugin', Plugin::$name);
    }

    /**
     * Test that the $description static property is set correctly.
     *
     * @return void
     */
    public function testDescriptionProperty(): void
    {
        $this->assertSame('Allows handling of NoVNC HTML5 VNC Connections', Plugin::$description);
    }

    /**
     * Test that the $help static property is an empty string.
     *
     * @return void
     */
    public function testHelpProperty(): void
    {
        $this->assertSame('', Plugin::$help);
    }

    /**
     * Test that the $type static property is set to 'plugin'.
     *
     * @return void
     */
    public function testTypeProperty(): void
    {
        $this->assertSame('plugin', Plugin::$type);
    }

    /**
     * Test that all expected static properties exist on the class.
     *
     * @return void
     */
    public function testStaticPropertiesExist(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $staticProperties = $reflection->getStaticProperties();

        $this->assertArrayHasKey('name', $staticProperties);
        $this->assertArrayHasKey('description', $staticProperties);
        $this->assertArrayHasKey('help', $staticProperties);
        $this->assertArrayHasKey('type', $staticProperties);
    }

    /**
     * Test that all static properties are public.
     *
     * @return void
     */
    public function testStaticPropertiesArePublic(): void
    {
        $reflection = new ReflectionClass(Plugin::class);

        foreach (['name', 'description', 'help', 'type'] as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isPublic(), "Property \${$propertyName} should be public");
            $this->assertTrue($property->isStatic(), "Property \${$propertyName} should be static");
        }
    }

    /**
     * Test that all static property values are strings.
     *
     * @return void
     */
    public function testStaticPropertyTypes(): void
    {
        $this->assertIsString(Plugin::$name);
        $this->assertIsString(Plugin::$description);
        $this->assertIsString(Plugin::$help);
        $this->assertIsString(Plugin::$type);
    }

    /**
     * Test that getHooks returns an array.
     *
     * @return void
     */
    public function testGetHooksReturnsArray(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertIsArray($hooks);
    }

    /**
     * Test that getHooks returns an empty array (all hooks are commented out).
     *
     * @return void
     */
    public function testGetHooksIsEmpty(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertEmpty($hooks);
    }

    /**
     * Test that the getHooks method is static.
     *
     * @return void
     */
    public function testGetHooksIsStatic(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $method = $reflection->getMethod('getHooks');
        $this->assertTrue($method->isStatic());
    }

    /**
     * Test that the getMenu method exists and is static.
     *
     * @return void
     */
    public function testGetMenuMethodSignature(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $method = $reflection->getMethod('getMenu');

        $this->assertTrue($method->isStatic(), 'getMenu should be static');
        $this->assertTrue($method->isPublic(), 'getMenu should be public');
        $this->assertCount(1, $method->getParameters(), 'getMenu should accept exactly one parameter');

        $param = $method->getParameters()[0];
        $this->assertSame('event', $param->getName());
    }

    /**
     * Test that getMenu parameter has the correct type hint.
     *
     * @return void
     */
    public function testGetMenuParameterType(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $method = $reflection->getMethod('getMenu');
        $param = $method->getParameters()[0];

        $this->assertTrue($param->hasType(), 'event parameter should have a type hint');
        $this->assertSame(
            GenericEvent::class,
            $param->getType()->getName(),
            'event parameter should be typed as GenericEvent'
        );
    }

    /**
     * Test that the getRequirements method exists and is static.
     *
     * @return void
     */
    public function testGetRequirementsMethodSignature(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $method = $reflection->getMethod('getRequirements');

        $this->assertTrue($method->isStatic(), 'getRequirements should be static');
        $this->assertTrue($method->isPublic(), 'getRequirements should be public');
        $this->assertCount(1, $method->getParameters(), 'getRequirements should accept exactly one parameter');

        $param = $method->getParameters()[0];
        $this->assertSame('event', $param->getName());
    }

    /**
     * Test that getRequirements parameter has the correct type hint.
     *
     * @return void
     */
    public function testGetRequirementsParameterType(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $method = $reflection->getMethod('getRequirements');
        $param = $method->getParameters()[0];

        $this->assertTrue($param->hasType(), 'event parameter should have a type hint');
        $this->assertSame(
            GenericEvent::class,
            $param->getType()->getName(),
            'event parameter should be typed as GenericEvent'
        );
    }

    /**
     * Test that the getSettings method exists and is static.
     *
     * @return void
     */
    public function testGetSettingsMethodSignature(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $method = $reflection->getMethod('getSettings');

        $this->assertTrue($method->isStatic(), 'getSettings should be static');
        $this->assertTrue($method->isPublic(), 'getSettings should be public');
        $this->assertCount(1, $method->getParameters(), 'getSettings should accept exactly one parameter');

        $param = $method->getParameters()[0];
        $this->assertSame('event', $param->getName());
    }

    /**
     * Test that getSettings parameter has the correct type hint.
     *
     * @return void
     */
    public function testGetSettingsParameterType(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $method = $reflection->getMethod('getSettings');
        $param = $method->getParameters()[0];

        $this->assertTrue($param->hasType(), 'event parameter should have a type hint');
        $this->assertSame(
            GenericEvent::class,
            $param->getType()->getName(),
            'event parameter should be typed as GenericEvent'
        );
    }

    /**
     * Test that getRequirements calls add_requirement on the loader subject.
     *
     * Uses an anonymous class to simulate the loader without mocking vendor classes.
     *
     * @return void
     */
    public function testGetRequirementsRegistersExpectedRequirements(): void
    {
        $loader = new class {
            /** @var array<int, array{name: string, path: string}> */
            public array $requirements = [];

            /**
             * @param string $name
             * @param string $path
             * @return void
             */
            public function add_requirement(string $name, string $path): void
            {
                $this->requirements[] = ['name' => $name, 'path' => $path];
            }
        };

        $event = new GenericEvent($loader);
        Plugin::getRequirements($event);

        $this->assertCount(4, $loader->requirements, 'Should register exactly 4 requirements');

        $names = array_column($loader->requirements, 'name');
        $this->assertContains('class.Novnc', $names);
        $this->assertContains('deactivate_kcare', $names);
        $this->assertContains('deactivate_abuse', $names);
        $this->assertContains('get_abuse_licenses', $names);
    }

    /**
     * Test that getRequirements registers paths containing expected file references.
     *
     * @return void
     */
    public function testGetRequirementsRegistersPaths(): void
    {
        $loader = new class {
            /** @var array<int, array{name: string, path: string}> */
            public array $requirements = [];

            /**
             * @param string $name
             * @param string $path
             * @return void
             */
            public function add_requirement(string $name, string $path): void
            {
                $this->requirements[] = ['name' => $name, 'path' => $path];
            }
        };

        $event = new GenericEvent($loader);
        Plugin::getRequirements($event);

        $paths = array_column($loader->requirements, 'path');
        foreach ($paths as $path) {
            $this->assertStringContainsString('myadmin-novnc-plugin', $path);
        }
    }

    /**
     * Test that getSettings retrieves the subject from the event without errors.
     *
     * @return void
     */
    public function testGetSettingsAcceptsEvent(): void
    {
        $settings = new class {
        };

        $event = new GenericEvent($settings);

        // getSettings should execute without throwing any exception
        Plugin::getSettings($event);
        $this->assertTrue(true, 'getSettings executed without error');
    }

    /**
     * Test that the constructor has no required parameters.
     *
     * @return void
     */
    public function testConstructorHasNoRequiredParameters(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor, 'Constructor should exist');
        $this->assertCount(0, $constructor->getParameters(), 'Constructor should have no parameters');
    }

    /**
     * Test that the Plugin class is in the correct namespace.
     *
     * @return void
     */
    public function testClassNamespace(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertSame('Detain\\MyAdminNovnc', $reflection->getNamespaceName());
    }

    /**
     * Test that the Plugin class is not abstract.
     *
     * @return void
     */
    public function testClassIsNotAbstract(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertFalse($reflection->isAbstract());
    }

    /**
     * Test that the Plugin class is not an interface.
     *
     * @return void
     */
    public function testClassIsNotInterface(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertFalse($reflection->isInterface());
    }

    /**
     * Test the total number of public methods on the class.
     *
     * @return void
     */
    public function testPublicMethodCount(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $publicMethods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        // Filter to only methods declared in Plugin class, not inherited
        $ownMethods = array_filter($publicMethods, function ($method) {
            return $method->getDeclaringClass()->getName() === Plugin::class;
        });

        $this->assertCount(5, $ownMethods, 'Plugin should have exactly 5 own public methods');
    }

    /**
     * Test that expected methods exist on the class.
     *
     * @return void
     */
    public function testExpectedMethodsExist(): void
    {
        $expectedMethods = ['__construct', 'getHooks', 'getMenu', 'getRequirements', 'getSettings'];

        $reflection = new ReflectionClass(Plugin::class);
        foreach ($expectedMethods as $methodName) {
            $this->assertTrue(
                $reflection->hasMethod($methodName),
                "Method {$methodName} should exist on Plugin class"
            );
        }
    }

    /**
     * Test that all event handler methods have void return or no return type.
     *
     * @return void
     */
    public function testEventHandlerReturnTypes(): void
    {
        $reflection = new ReflectionClass(Plugin::class);

        foreach (['getMenu', 'getRequirements', 'getSettings'] as $methodName) {
            $method = $reflection->getMethod($methodName);
            $returnType = $method->getReturnType();

            // These methods either have no return type or return void
            if ($returnType !== null) {
                $this->assertSame('void', $returnType->getName(), "{$methodName} should return void if typed");
            } else {
                $this->assertNull($returnType, "{$methodName} has no return type declaration");
            }
        }
    }

    /**
     * Test that getHooks return type is array.
     *
     * @return void
     */
    public function testGetHooksReturnType(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $method = $reflection->getMethod('getHooks');
        $returnType = $method->getReturnType();

        // getHooks may or may not have a declared return type, but it returns an array
        $result = Plugin::getHooks();
        $this->assertIsArray($result);
    }

    /**
     * Test that multiple instantiations are independent.
     *
     * @return void
     */
    public function testMultipleInstantiationsAreIndependent(): void
    {
        $plugin1 = new Plugin();
        $plugin2 = new Plugin();

        $this->assertNotSame($plugin1, $plugin2);
        $this->assertInstanceOf(Plugin::class, $plugin1);
        $this->assertInstanceOf(Plugin::class, $plugin2);
    }

    /**
     * Test that static properties are shared across instances.
     *
     * @return void
     */
    public function testStaticPropertiesSharedAcrossInstances(): void
    {
        $original = Plugin::$name;

        Plugin::$name = 'Test Override';
        $this->assertSame('Test Override', Plugin::$name);

        // Restore original value
        Plugin::$name = $original;
        $this->assertSame('Novnc Plugin', Plugin::$name);
    }

    /**
     * Test that the class file uses the correct namespace declaration.
     *
     * @return void
     */
    public function testSourceFileExists(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertFileExists($reflection->getFileName());
    }

    /**
     * Test that getRequirements adds the Novnc class path correctly.
     *
     * @return void
     */
    public function testGetRequirementsNovncClassPath(): void
    {
        $loader = new class {
            /** @var array<int, array{name: string, path: string}> */
            public array $requirements = [];

            /**
             * @param string $name
             * @param string $path
             * @return void
             */
            public function add_requirement(string $name, string $path): void
            {
                $this->requirements[] = ['name' => $name, 'path' => $path];
            }
        };

        $event = new GenericEvent($loader);
        Plugin::getRequirements($event);

        $novncReq = null;
        foreach ($loader->requirements as $req) {
            if ($req['name'] === 'class.Novnc') {
                $novncReq = $req;
                break;
            }
        }

        $this->assertNotNull($novncReq, 'class.Novnc requirement should be registered');
        $this->assertStringContainsString('Novnc.php', $novncReq['path']);
    }

    /**
     * Test that getRequirements registers abuse-related requirements with the correct file.
     *
     * @return void
     */
    public function testGetRequirementsAbuseFilePaths(): void
    {
        $loader = new class {
            /** @var array<int, array{name: string, path: string}> */
            public array $requirements = [];

            /**
             * @param string $name
             * @param string $path
             * @return void
             */
            public function add_requirement(string $name, string $path): void
            {
                $this->requirements[] = ['name' => $name, 'path' => $path];
            }
        };

        $event = new GenericEvent($loader);
        Plugin::getRequirements($event);

        $abuseNames = ['deactivate_kcare', 'deactivate_abuse', 'get_abuse_licenses'];
        foreach ($loader->requirements as $req) {
            if (in_array($req['name'], $abuseNames, true)) {
                $this->assertStringContainsString(
                    'abuse.inc.php',
                    $req['path'],
                    "{$req['name']} should reference abuse.inc.php"
                );
            }
        }
    }
}
