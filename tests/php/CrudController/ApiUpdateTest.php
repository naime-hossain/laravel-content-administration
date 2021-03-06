<?php

namespace FjordTest\CrudController;

use FjordTest\FrontendTestCase;
use FjordTest\TestSupport\Models\Post;
use FjordTest\Traits\InteractsWithCrud;

/**
 * This test is using the Crud Post.
 *
 * @see FjordApp\Config\Crud\PostConfig
 * @see FjordTest\TestSupport\Models\Post
 */
class ApiUpdateTest extends FrontendTestCase
{
    use InteractsWithCrud;

    public function setUp(): void
    {
        parent::setUp();

        $this->post = Post::create([]);
        $this->actingAs($this->admin, 'fjord');
    }

    public function refreshModel()
    {
        $this->post = $this->post->fresh();
    }

    /** @test */
    public function test_update()
    {
        $url = $this->getCrudRoute("/{$this->post->id}/api/show");
        $response = $this->put($url);
        $response->assertStatus(200);
    }

    /** @test */
    public function test_update_method_updates_attribute()
    {
        $url = $this->getCrudRoute("/{$this->post->id}/api/show");

        $response = $this->put($url, ['payload' => ['title' => 'dummy title']]);
        $response->assertStatus(200);

        $this->refreshModel();
        $this->assertEquals($this->post->title, 'dummy title');
    }
}
