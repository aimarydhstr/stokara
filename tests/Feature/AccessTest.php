<?php
namespace Tests\Feature;
use Tests\TestCase;
class AccessTest extends TestCase
{
    public function test_guest_cannot_access_inventory_pages(): void
    {
        foreach (['/', '/items', '/categories', '/movements', '/movements/export', '/movements/create'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }
}