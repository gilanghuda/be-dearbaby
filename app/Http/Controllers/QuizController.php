<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\Attempt;
use App\Models\Answer;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quizzes = Quiz::select('id', 'title', 'description')->get();
        return response()->json($quizzes);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->family_role !== 'admin') {
            return response()->json(['message' => 'Only admin can create quiz'], 403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*' => 'required|string',
            'questions.*.correctAnswer' => 'required|string',
        ]);
        // Create quiz
        $quiz = Quiz::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ]);
        // Create questions and options
        foreach ($validated['questions'] as $q) {
            $question = Question::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'quiz_id' => $quiz->id,
                'question_text' => $q['question'],
            ]);
            foreach ($q['options'] as $opt) {
                Option::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'question_id' => $question->id,
                    'option_text' => $opt,
                    'is_correct' => $opt === $q['correctAnswer'],
                ]);
            }
        }
        return response()->json(['message' => 'Quiz created', 'quiz' => $quiz], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $quiz = Quiz::where('id', $id)->first();
        if (!$quiz) {
            return response()->json(['message' => 'Quiz not found'], 404);
        }
        $questions = Question::where('quiz_id', $quiz->id)->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'question' => $q->question_text,
                'options' => Option::where('question_id', $q->id)->pluck('option_text'),
            ];
        });
        return response()->json([
            'id' => $quiz->id,
            'title' => $quiz->title,
            'questions' => $questions,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        //
    }

    // Submit answers to a quiz
    public function submit(Request $request, $quizId)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $validated = $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.questionId' => 'required|string|exists:questions,id',
            'answers.*.selectedOption' => 'required|string',
        ]);
        $attempt = Attempt::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->user_id,
            'quiz_id' => $quizId,
            'score' => 0,
            'total_questions' => count($validated['answers']),
            'submitted_at' => now(),
        ]);
        $score = 0;
        foreach ($validated['answers'] as $ans) {
            $question = Question::find($ans['questionId']);
            $option = Option::where('question_id', $question->id)
                ->where('option_text', $ans['selectedOption'])->first();
            $isCorrect = $option && $option->is_correct;
            if ($isCorrect) $score++;
            Answer::create([
                'id' => (string) Str::uuid(),
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'selected_option_id' => $option ? $option->id : null,
                'is_correct' => $isCorrect,
            ]);
        }
        $attempt->score = $score;
        $attempt->save();
        return response()->json([
            'message' => 'Quiz submitted',
            'score' => $score,
            'total' => $attempt->total_questions,
        ]);
    }

    // Get user's quiz history
    public function history(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $attempts = Attempt::where('user_id', $user->user_id)
            ->with('quiz')
            ->orderByDesc('submitted_at')
            ->get()
            ->map(function ($a) {
                $isComplete = $a->total_questions > 0 && ($a->score / $a->total_questions) >= 0.8;
                return [
                    'quizId' => $a->quiz_id,
                    'title' => optional($a->quiz)->title,
                    'score' => $a->score,
                    'total' => $a->total_questions,
                    'date' => $a->submitted_at,
                    'complete' => $isComplete,
                ];
            });
        return response()->json($attempts);
    }
}
