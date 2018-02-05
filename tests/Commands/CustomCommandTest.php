<?php

namespace Eyewitness\Eye\Test\Commands;

use Mockery;
use Eyewitness\Eye\Eye;
use Eyewitness\Eye\Test\TestCase;
use Eyewitness\Eye\Monitors\Custom;
use Illuminate\Support\Facades\Artisan;
use Eyewitness\Eye\Repo\CustomWitnessHistory;

class CustomCommandTest extends TestCase
{
    protected $eye;
    protected $witness;

    public function setUp()
    {
        parent::setUp();

        $this->eye = Mockery::mock(Eye::class);
        $this->app->instance(Eye::class, $this->eye);

        $this->witness = Mockery::mock(MyMock::class);
    }

    public function test_command_handles_empty_list()
    {
        $this->eye->shouldReceive('getCustomWitnesses')->with(true)->once()->andReturn([]);

        $this->artisan('eyewitness:custom');

        $output = Artisan::output();

        $this->assertTrue(str_contains($output, 'Starting Custom Witness command...'));
        $this->assertTrue(str_contains($output, 'Custom Witness command complete...'));
        $this->assertFalse(str_contains($output, 'Running:'));
    }

    public function test_command_runs_custom_witness_with_success()
    {
        $this->witness->shouldReceive('getSafeName')->andReturn('example_name');
        $this->witness->shouldReceive('run')->once()->andReturn(true);
        $this->witness->shouldReceive('saveHistory')->with(true);
        $this->witness->shouldReceive('checkHealth')->with(true);

        $this->eye->shouldReceive('getCustomWitnesses')->with(true)->once()->andReturn([$this->witness]);

        $this->artisan('eyewitness:custom');

        $output = Artisan::output();

        $this->assertTrue(str_contains($output, 'Starting Custom Witness command...'));
        $this->assertTrue(str_contains($output, 'Custom Witness command complete...'));
        $this->assertTrue(str_contains($output, 'Running: example_name'));
    }

    public function test_command_runs_custom_witness_with_failure()
    {
        $this->witness->shouldReceive('getSafeName')->andReturn('example_name');
        $this->witness->shouldReceive('run')->once()->andReturn(false);
        $this->witness->shouldReceive('saveHistory')->with(false);
        $this->witness->shouldReceive('checkHealth')->with(false);

        $this->eye->shouldReceive('getCustomWitnesses')->with(true)->once()->andReturn([$this->witness]);

        $this->artisan('eyewitness:custom');

        $output = Artisan::output();

        $this->assertTrue(str_contains($output, 'Starting Custom Witness command...'));
        $this->assertTrue(str_contains($output, 'Custom Witness command complete...'));
        $this->assertTrue(str_contains($output, 'Running: example_name'));
    }
}


class MyMock extends Custom
{
    public function run()
    {
        //
    }
}
