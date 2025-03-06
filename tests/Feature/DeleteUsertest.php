<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class DeleteUsertest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_suppression_reussie_avec_token_administrateur()
    {
        // Création d'un administrateur
        $admin = User::factory()->create(['is_admin' => true]);

        // Création d'un utilisateur à supprimer
        $user = User::factory()->create();

        // Authentification de l'admin
        Sanctum::actingAs($admin);

        // Requête DELETE
        $response = $this->deleteJson("/api/users/{$user->id}");

        // Vérifications
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Utilisateur supprimé']);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function test_suppression_echouee_sans_token()
    {
        // Création d'un utilisateur
        $user = User::factory()->create();

        // Requête DELETE sans authentification
        $response = $this->deleteJson("/api/users/{$user->id}");

        // Vérifications
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Non autorisé']);
    }

    /** @test */
    public function test_suppression_echouee_utilisateur_inexistant()
    {
        // Création d'un administrateur pour authentification
        $admin = User::factory()->create(['is_admin' => true]);

        // Authentification
        Sanctum::actingAs($admin);

        // ID inexistant
        $nonExistentUserId = 999;

        // Requête DELETE sur un ID inexistant
        $response = $this->deleteJson("/api/users/{$nonExistentUserId}");

        // Vérifications
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Utilisateur non trouvé']);
    }

    /** @test */
    public function test_suppression_echouee_avec_token_utilisateur_normal()
    {
        // Création d'un utilisateur non admin
        $user = User::factory()->create(['is_admin' => false]);

        // Création d'un autre utilisateur à supprimer
        $targetUser = User::factory()->create();

        // Authentification avec un utilisateur normal
        Sanctum::actingAs($user);

        // Requête DELETE
        $response = $this->deleteJson("/api/users/{$targetUser->id}");

        // Vérifications
        $response->assertStatus(403);
        $response->assertJson(['message' => 'Accès interdit']);
    }
}
