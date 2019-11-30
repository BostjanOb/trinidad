<?php

namespace Tests\Feature\Controllers;

use App\Domain;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DomainsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function indexListAllDomains()
    {
        factory(Domain::class, 10)->create();

        $this->asUser()
            ->json('GET', '/api/domains')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['total' => 10])
            ->assertJsonStructure(['data' => ['*' => ['id', 'domain', 'valid_to']]]);
    }

    /** @test */
    public function userCanNotAddDomain()
    {
        $this->asUser()
            ->json('POST', '/api/domains', ['domain' => 'example.com'])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function validateDomainName()
    {
        $this->asAdmin()
            ->json('POST', '/api/domains', ['domain' => 'invalid domain.com'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function addNewDomain()
    {
        $this->asAdmin()
            ->json('POST', '/api/domains', ['domain' => 'example.com'])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['data' => ['id', 'domain']]);

        $this->assertDatabaseHas('domains', ['domain' => 'example.com']);
    }

    /** @test */
    public function showReturnsNotFoundForInvalidId()
    {
        $this->asUser()
            ->json('GET', '/api/domains/3')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function showReturnDomainResponse()
    {
        $domain = factory(Domain::class)->create();

        $this->asUser()
            ->json('GET', '/api/domains/'.$domain->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => ['id', 'domain', 'valid_to']]);
    }

    /** @test */
    public function deleteUserIsForbidden()
    {
        $domain = factory(Domain::class)->create();

        $this->asUser()
            ->json('DELETE', '/api/domains/'.$domain->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function deleteAdminCanDeleteDomain()
    {
        $domain = factory(Domain::class)->create();

        $this->asAdmin()
            ->json('DELETE', '/api/domains/'.$domain->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
