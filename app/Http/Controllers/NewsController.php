<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\NewsArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $query = NewsArticle::with(['author', 'match.homeTeam', 'match.awayTeam']);

        if ($request->filter === 'incidents') {
            $query->where('match_incident_flag', true);
        } elseif ($request->filter === 'announcements') {
            $query->where('match_incident_flag', false);
        }

        $articles = $query->orderBy('published_at', 'desc')->paginate(8);
        $recentMatches = GameMatch::where('status', 'completed')->take(10)->get();

        return view('pages.news.index', compact('articles', 'recentMatches'));
    }

    public function show(string $slug): View
    {
        $article = NewsArticle::where('slug', $slug)
            ->with(['author', 'match.homeTeam', 'match.awayTeam', 'match.tournament'])
            ->firstOrFail();

        $relatedArticles = NewsArticle::where('id', '!=', $article->id)
            ->where('match_incident_flag', $article->match_incident_flag)
            ->take(3)
            ->get();

        return view('pages.news.show', compact('article', 'relatedArticles'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create-tournament'); // Admin gate

        $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'match_incident_flag' => 'nullable|boolean',
            'match_id' => 'nullable|exists:game_matches,id',
        ]);

        $article = NewsArticle::create([
            'author_id' => Auth::id(),
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'excerpt' => $request->excerpt ?? Str::limit(strip_tags($request->body), 150),
            'body' => $request->body,
            'match_incident_flag' => $request->boolean('match_incident_flag'),
            'match_id' => $request->match_id,
            'published_at' => now(),
        ]);

        return redirect()->route('news.show', $article->slug)
            ->with('success', 'Article / Disciplinary Incident report published successfully!');
    }
}

