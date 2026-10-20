<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_renders_the_converter(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Fetch your fonts for the web')
            ->assertSee('data-converter', escape: false)
            ->assertSee('id="font-files"', escape: false)
            ->assertSee('Drop TTF or OTF fonts here');
    }

    public function test_legal_page_renders(): void
    {
        $this->get('/legal')
            ->assertOk()
            ->assertSee('The small print')
            ->assertSee('never uploaded');
    }

    public function test_og_image_source_is_hidden_outside_local_development(): void
    {
        $this->get('/og-image')->assertNotFound();
    }
}
