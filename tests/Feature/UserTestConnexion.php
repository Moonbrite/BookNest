<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase; // Réinitialise la base de données entre les tests

    public function test_1_connexion_ok(): void
    {
        // Créer un utilisateur en base de données
        $user = [
            'email' => 'test@example.com',
            'password' => bcrypt('password123'), // Le mot de passe doit être hashé
        ];

        // Envoyer une requête POST à /api/login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        // Vérifier le statut de réponse 200
        $response->assertStatus(200);

        // Vérifier que le token est bien retourné
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    public function test_2_connexion_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Identifiants invalides']);
    }
    
    public function test_3_connexion_avec_utilisateur_inexistant() :void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'fake@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Identifiants invalides']);
    }

    public function test_4_connexion_champs_vide() : void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => '',
            'password' => ''
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Email et mot de passe requis']);
    }
}
