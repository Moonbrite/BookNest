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
        $response = $this->postJson('/api/login', [
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

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Identifiants invalides']);
    }
    
}
