<?php

namespace Jam0r85\NovaCalendar\Tests;

class ToolControllerTest extends TestCase
{
    /** @test */
    public function it_can_can_return_a_response()
    {
        $this
            ->get('nova-vendor/Jam0r85/nova-calendar/endpoint')
            ->assertSuccessful();
    }
}
