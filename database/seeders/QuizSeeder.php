<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\User;
use App\Models\Attempt;
use App\Models\Answer;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user dummy jika belum ada
        $user = User::first() ?? User::factory()->create([
            'username' => 'quizuser',
            'email' => 'quizuser@example.com',
            'phone_number' => '08123456789',
            'password' => bcrypt('password'),
            'family_role' => 'user',
        ]);

        // Quiz 1
        $quiz1 = Quiz::create([
            'id' => (string) Str::uuid(),
            'title' => 'Math Quiz',
            'description' => 'Simple math questions',
            'created_at' => now(),
        ]);
        $q1 = Question::create([
            'id' => (string) Str::uuid(),
            'quiz_id' => $quiz1->id,
            'question_text' => 'What is 2 + 2?',
            'created_at' => now(),
        ]);
        $opt1 = Option::create([
            'id' => (string) Str::uuid(),
            'question_id' => $q1->id,
            'option_text' => '3',
            'is_correct' => false,
        ]);
        $opt2 = Option::create([
            'id' => (string) Str::uuid(),
            'question_id' => $q1->id,
            'option_text' => '4',
            'is_correct' => true,
        ]);
        $opt3 = Option::create([
            'id' => (string) Str::uuid(),
            'question_id' => $q1->id,
            'option_text' => '5',
            'is_correct' => false,
        ]);
        // Quiz 2
        $quiz2 = Quiz::create([
            'id' => (string) Str::uuid(),
            'title' => 'Science Quiz',
            'description' => 'Simple science questions',
            'created_at' => now(),
        ]);
        $q2 = Question::create([
            'id' => (string) Str::uuid(),
            'quiz_id' => $quiz2->id,
            'question_text' => 'What planet is known as the Red Planet?',
            'created_at' => now(),
        ]);
        $opt4 = Option::create([
            'id' => (string) Str::uuid(),
            'question_id' => $q2->id,
            'option_text' => 'Mars',
            'is_correct' => true,
        ]);
        $opt5 = Option::create([
            'id' => (string) Str::uuid(),
            'question_id' => $q2->id,
            'option_text' => 'Venus',
            'is_correct' => false,
        ]);
        $opt6 = Option::create([
            'id' => (string) Str::uuid(),
            'question_id' => $q2->id,
            'option_text' => 'Jupiter',
            'is_correct' => false,
        ]);

        // Attempt & Answer untuk user pada quiz1
        $attempt1 = Attempt::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->user_id,
            'quiz_id' => $quiz1->id,
            'score' => 1,
            'total_questions' => 1,
            'submitted_at' => now(),
        ]);
        Answer::create([
            'id' => (string) Str::uuid(),
            'attempt_id' => $attempt1->id,
            'question_id' => $q1->id,
            'selected_option_id' => $opt2->id,
            'is_correct' => true,
        ]);

        // Attempt & Answer untuk user pada quiz2
        $attempt2 = Attempt::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->user_id,
            'quiz_id' => $quiz2->id,
            'score' => 1,
            'total_questions' => 1,
            'submitted_at' => now(),
        ]);
        Answer::create([
            'id' => (string) Str::uuid(),
            'attempt_id' => $attempt2->id,
            'question_id' => $q2->id,
            'selected_option_id' => $opt4->id,
            'is_correct' => true,
        ]);
    }
}
