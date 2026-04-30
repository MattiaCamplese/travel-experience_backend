<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Utente fisso per il login di test
        $mainUser = User::factory()->create([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Altri 4 utenti casuali
        $users = User::factory(4)->create();
        $allUsers = $users->push($mainUser);

        // Ogni utente crea 3 post
        $posts = collect();
        foreach ($allUsers as $user) {
            $userPosts = Post::factory(3)->create(['user_id' => $user->id]);
            $posts = $posts->merge($userPosts);
        }

        // Commenti e like sui post
        foreach ($posts as $post) {
            // 2-4 commenti per post
            $commenters = $allUsers->random(rand(2, 4));
            foreach ($commenters as $commenter) {
                $comment = Comment::factory()->create([
                    'user_id' => $commenter->id,
                    'post_id' => $post->id,
                ]);

                // Qualche like ai commenti
                $likers = $allUsers->random(rand(0, 2));
                foreach ($likers as $liker) {
                    CommentLike::firstOrCreate([
                        'user_id' => $liker->id,
                        'comment_id' => $comment->id,
                    ]);
                }
            }

            // 1-3 like al post
            $likers = $allUsers->random(rand(1, 3));
            foreach ($likers as $liker) {
                Like::firstOrCreate([
                    'user_id' => $liker->id,
                    'post_id' => $post->id,
                ]);
            }
        }
    }
}
