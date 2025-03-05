<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTestRegister extends TestCase
{
    use RefreshDatabase; // Réinitialise la base de données entre les tests

    public function test_1_inscription_ok(): void
    {
        // Créer un utilisateur en base de données
        $user = [
            'email' => 'newuser@example.com',
            'password' => bcrypt('password123'), // Le mot de passe doit être hashé
            "name" => "New User"
        ];
        // Envoyer une requête POST à /api/auth/register
        $response = $this->postJson('/api/auth/register', [
            'email' => 'newuser@example.com',
            'password' => 'password123',
            "name" => "New User"
        ]);

        // Vérifier le statut de réponse 201
        $response->assertStatus(201);

        // Vérifier que le token est bien retourné
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    public function test_2_inscription_avec_email_existant(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Envoyer une requête POST à /api/auth/register
        $response = $this->postJson('/api/auth/register', [
            'email' => 'newuser@example.com',
            'password' => 'password123',
            "name" => "New User2"
        ]);

        // Vérifier le statut de réponse 400
        $response->assertStatus(400);

            // Vérifier que le message d'erreur est retourné
            $response->assertJson(['message' => 'Email déjà utilisé']);
    }
}