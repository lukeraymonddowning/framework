<?php

namespace Illuminate\Tests\View\Blade;

use Exception;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

class BladeEchoHandlerTest extends AbstractBladeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->compiler->stringable(function (Fluent $object) {
            return 'Hello World';
        });
    }

    public function testBladeHandlerCanInterceptRegularEchos()
    {
        $this->assertSame(
            "<?php echo e(app('blade.compiler')->applyEchoHandler(\$exampleObject)); ?>",
            $this->compiler->compileString('{{$exampleObject}}')
        );
    }

    public function testBladeHandlerCanInterceptRawEchos()
    {
        $this->assertSame(
            "<?php echo app('blade.compiler')->applyEchoHandler(\$exampleObject); ?>",
            $this->compiler->compileString('{!!$exampleObject!!}')
        );
    }

    public function testBladeHandlerCanInterceptEscapedEchos()
    {
        $this->assertSame(
            "<?php echo e(app('blade.compiler')->applyEchoHandler(\$exampleObject)); ?>",
            $this->compiler->compileString('{{{$exampleObject}}}')
        );
    }

    public function testWhitespaceIsPreservedCorrectly()
    {
        $this->assertSame(
            "<?php echo e(app('blade.compiler')->applyEchoHandler(\$exampleObject)); ?>\n\n",
            $this->compiler->compileString("{{\$exampleObject}}\n")
        );
    }

    public function testHandlerLogicWorksCorrectly()
    {
        $this->expectExceptionMessage('The fluent object has been successfully handled!');

        $this->compiler->stringable(Fluent::class, function ($object) {
            throw new Exception('The fluent object has been successfully handled!');
        });

        app()->singleton('blade.compiler', function () {
            return $this->compiler;
        });

        $exampleObject = new Fluent();

        eval(
            Str::of($this->compiler->compileString('{{$exampleObject}}'))
            ->after('<?php')
            ->beforeLast('?>')
        );
    }

    public function testHandlerLogicWorksCorrectlyWithSemicolon()
    {
        $this->expectExceptionMessage('The fluent object has been successfully handled!');

        $this->compiler->stringable(Fluent::class, function ($object) {
            throw new Exception('The fluent object has been successfully handled!');
        });

        app()->singleton('blade.compiler', function () {
            return $this->compiler;
        });

        $exampleObject = new Fluent();

        eval(
        Str::of($this->compiler->compileString('{{$exampleObject;}}'))
            ->after('<?php')
            ->beforeLast('?>')
        );
    }
}
