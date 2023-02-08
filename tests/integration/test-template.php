<?php

use WP_Hashids\Template;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class Template_Test extends TestCase
{
    /** @test */
    public function it_renders_templates_without_data() {
        $template = new Template( __DIR__ . '/../fixtures' );

        $rendered = $template->render( 'test-template' );

        $this->assertSame( 'Hello World', $rendered );
    }

    /** @test */
    public function it_renders_templates_with_data() {
        $template = new Template( __DIR__ . '/../fixtures' );

        $rendered = $template->render( 'test-template', [ 'name' => 'Joe' ] );

        $this->assertSame( 'Hello Joe', $rendered );
    }
}
