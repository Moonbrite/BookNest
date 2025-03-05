<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class GetUsertest extends TestCase
{
    // use RefreshDatabase;

    /** @test */
    public function test_acces_autorise_avec_un_token_valide()
    {
        // Création d'un utilisateur avec Sanctum pour générer un token
        $user = User::factory()->create();

        // Authentification via Sanctum
        Sanctum::actingAs($user);

        // Requête GET avec le token
        $response = $this->getJson('/api/users');

        // Vérifications
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'email', 'created_at', 'updated_at']
        ]);
    }

    /** @test */
    public function test_acces_refuse_sans_token()
    {
        // Requête GET sans authentification
        $response = $this->getJson('/api/users');

        // Vérifications
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Non autorisé']);
    }

    /** @test */
    public function test_acces_refuse_avec_un_token_invalide()
    {
        // Envoi d'une requête avec un token bidon
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token'
        ])->getJson('/api/users');

        // Vérifications
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Token invalide ou expiré']);
    }
}
