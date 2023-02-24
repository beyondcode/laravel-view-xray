<?php

namespace BeyondCode\ViewXray\Tests;

use Route;
use Spatie\Snapshots\MatchesSnapshots;
use BeyondCode\ViewXray\Events\InjectedXrayBar;
use Illuminate\Support\Facades\Event;

class XrayTest extends TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function it_adds_xray_comments_to_the_output()
    {
        Route::get('/', function() {
            return view('example');
        });

        $response = $this->get('/');

        $this->assertMatchesSnapshot($response->getContent());
    }

    /** @test */
    public function it_does_not_apply_middleware_on_json_responses()
    {
        $data = [
            'foo' => 'bar'
        ];

        Route::get('/', function() use ($data) {
            return $data;
        });

        $this->get('/')->assertJson($data);
    }

    /** @test */
    public function it_adds_xray_when_using_parenthesis_on_sections() 
    {
        Route::get('/', function() {
            return view('example2');
        });
        
        $response = $this->get('/');
        
        $this->assertMatchesSnapshot($response->getContent());
    }
    
    /** @test */
    public function it_fires_an_event_if_injected_xray_bar()
    {
        Event::fake();

        Route::get('/', function () {
            return view('example');
        });

        $this->get('/');

        Event::assertDispatched(InjectedXrayBar::class);
    }

    /** @test */
    public function it_does_not_fire_an_event_if_injected_xray_bar()
    {
        Event::fake();

        $data = [
            'foo' => 'bar'
        ];

        Route::get('/', function () use ($data) {
            return $data;
        });

        $this->get('/');

        Event::assertNotDispatched(InjectedXrayBar::class);
    }
}
