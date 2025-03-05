<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class GetOneUsertest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_recuperation_reussie_utilisateur_valide_avec_token()
    {
        // Création d'un utilisateur
        $user = User::factory()->create();

        // Authentification avec Sanctum
        Sanctum::actingAs($user);

        // Requête GET pour récupérer l'utilisateur
        $response = $this->getJson("/api/users/{$user->id}");

        // Vérifications
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function test_recuperation_echoue_sans_token()
    {
        // Création d'un utilisateur
        $user = User::factory()->create();

        // Requête GET sans authentification
        $response = $this->getJson("/api/users/{$user->id}");

        // Vérifications
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Non autorisé']);
    }

    /** @test */
    public function test_recuperation_echoue_utilisateur_inexistant()
    {
        // Création d'un utilisateur pour s'authentifier
        $user = User::factory()->create();

        // Authentification avec Sanctum
        Sanctum::actingAs($user);

        // ID inexistant
        $nonExistentUserId = 999;

        // Requête GET avec un ID inexistant
        $response = $this->getJson("/api/users/{$nonExistentUserId}");

        // Vérifications
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Utilisateur non trouvé']);
    }
}
