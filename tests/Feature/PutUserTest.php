<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class PutUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_modification_reussie_avec_token_valide()
    {
        // Création d'un utilisateur administrateur
        $admin = User::factory()->create(['is_admin' => true]);

        // Création d'un utilisateur à modifier
        $user = User::factory()->create();

        // Authentification avec Sanctum en tant qu'admin
        Sanctum::actingAs($admin);

        // Données mises à jour
        $updatedData = [
            "name" => "Updated Name",
            "email" => "updated@example.com"
        ];

        // Requête PUT
        $response = $this->putJson("/api/users/{$user->id}", $updatedData);

        // Vérifications
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Utilisateur mis à jour']);
        $this->assertDatabaseHas('users', $updatedData);
    }

    /** @test */
    public function test_modification_echouee_sans_token()
    {
        // Création d'un utilisateur
        $user = User::factory()->create();

        // Données mises à jour
        $updatedData = [
            "name" => "Updated Name",
            "email" => "updated@example.com"
        ];

        // Requête PUT sans authentification
        $response = $this->putJson("/api/users/{$user->id}", $updatedData);

        // Vérifications
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Non autorisé']);
    }

    /** @test */
    public function test_modification_echouee_utilisateur_inexistant()
    {
        // Création d'un admin pour authentification
        $admin = User::factory()->create(['is_admin' => true]);

        // Authentification
        Sanctum::actingAs($admin);

        // ID inexistant
        $nonExistentUserId = 999;

        // Données mises à jour
        $updatedData = [
            "name" => "Updated Name",
            "email" => "updated@example.com"
        ];

        // Requête PUT avec un ID inexistant
        $response = $this->putJson("/api/users/{$nonExistentUserId}", $updatedData);

        // Vérifications
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Utilisateur non trouvé']);
    }

    /** @test */
    public function test_modification_echouee_avec_token_non_administrateur()
    {
        // Création d'un utilisateur non admin
        $user = User::factory()->create(['is_admin' => false]);

        // Création d'un autre utilisateur à modifier
        $targetUser = User::factory()->create();

        // Authentification avec Sanctum en tant qu'utilisateur non admin
        Sanctum::actingAs($user);

        // Données mises à jour
        $updatedData = [
            "name" => "Updated Name",
            "email" => "updated@example.com"
        ];

        // Requête PUT
        $response = $this->putJson("/api/users/{$targetUser->id}", $updatedData);

        // Vérifications
        $response->assertStatus(403);
        $response->assertJson(['message' => 'Accès interdit']);
    }
}
