<?php

namespace Illuminate\Tests\View\Blade;

use Illuminate\Support\Fluent;

class BladeEchoHandlerTest extends AbstractBladeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->compiler->handle(Fluent::class, function ($object) {
            return 'Hello World';
        });
    }

    public function testBladeHandlersCanBeAddedForAGivenClass()
    {
        $this->assertSame('Hello World', $this->compiler->echoHandlers[Fluent::class](new Fluent()));
    }

    public function testBladeHandlerCanInterceptRegularEchos()
    {
        $echoHandlerArray = "app('blade.compiler')->echoHandlers";

        $this->assertSame(
            "<?php echo e(is_object(\$exampleObject) && isset({$echoHandlerArray}[get_class(\$exampleObject)]) ? call_user_func_array({$echoHandlerArray}[get_class(\$exampleObject)], [\$exampleObject]) : \$exampleObject); ?>",
            $this->compiler->compileString('{{$exampleObject}}')
        );
    }

    public function testBladeHandlerCanInterceptRawEchos()
    {
        $echoHandlerArray = "app('blade.compiler')->echoHandlers";

        $this->assertSame(
            "<?php echo is_object(\$exampleObject) && isset({$echoHandlerArray}[get_class(\$exampleObject)]) ? call_user_func_array({$echoHandlerArray}[get_class(\$exampleObject)], [\$exampleObject]) : \$exampleObject; ?>",
            $this->compiler->compileString('{!!$exampleObject!!}')
        );
    }

    public function testBladeHandlerCanInterceptEscapedEchos()
    {
        $echoHandlerArray = "app('blade.compiler')->echoHandlers";

        $this->assertSame(
            "<?php echo e(is_object(\$exampleObject) && isset({$echoHandlerArray}[get_class(\$exampleObject)]) ? call_user_func_array({$echoHandlerArray}[get_class(\$exampleObject)], [\$exampleObject]) : \$exampleObject); ?>",
            $this->compiler->compileString('{{{$exampleObject}}}')
        );
    }

    public function testWhitespaceIsPreservedCorrectly()
    {
        $echoHandlerArray = "app('blade.compiler')->echoHandlers";

        $this->assertSame(
            "<?php echo e(is_object(\$exampleObject) && isset({$echoHandlerArray}[get_class(\$exampleObject)]) ? call_user_func_array({$echoHandlerArray}[get_class(\$exampleObject)], [\$exampleObject]) : \$exampleObject); ?>\n\n",
            $this->compiler->compileString("{{\$exampleObject}}\n")
        );
    }
}
