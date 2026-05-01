<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\GitHubRepo;
use App\Models\Project;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ProjectsController extends Controller
{
    public function index(Request $request): Factory | View
    {
        $tab = $request->query('tab', 'github');

        $languages = GitHubRepo::languages();
        $statuses = Project::statuses();

        $repos = GitHubRepo::query()
            ->withLanguage($request->query('language'))
            ->sortedBy($request->query('sort'));

        $limit = $request->query('limit', 10);
        if ($limit !== 'all') {
            $repos = $repos->limit((int) $limit);
        }

        $repos = $repos->get();

        $projects = Project::query()
            ->withStatus($request->query('status'))
            ->sortedBy($request->query('project_sort'))
            ->get();

        return view('projects', [
            'tab' => $tab,
            'repos' => $repos,
            'projects' => $projects,
            'languages' => $languages,
            'statuses' => $statuses,
        ]);
    }
}
