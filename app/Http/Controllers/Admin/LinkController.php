<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Link;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    /**
     * @var array<string, string>
     */
    private const SORT_COLUMNS = [
        'title' => 'links.title',
        'slug' => 'links.slug',
        'destination_url' => 'links.destination_url',
        'visibility' => 'links.visibility',
        'is_active' => 'links.is_active',
        'sort_order' => 'links.sort_order',
        'clicks_count' => 'links.clicks_count',
        'last_clicked_at' => 'links.last_clicked_at',
        'expires_at' => 'links.expires_at',
        'created_at' => 'links.created_at',
        'updated_at' => 'links.updated_at',
    ];

    public function index(Request $request): View
    {
        [$sort, $direction] = $this->sorting($request);

        $links = Link::query()
            ->with('platform')
            ->select('links.*')
            ->tap(fn (Builder $query) => $this->applySorting($query, $sort, $direction))
            ->orderByDesc('links.id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.links.index', [
            'direction' => $direction,
            'links' => $links,
            'sort' => $sort,
        ]);
    }

    public function show(Link $link): View
    {
        return view('admin.links.show', [
            'link' => $link->load('platform'),
        ]);
    }

    public function destroy(Link $link): RedirectResponse
    {
        $link->delete();

        return redirect()
            ->route('admin.links.index')
            ->with('status', __('Link deleted.'));
    }

    /**
     * @return array{0: string, 1: 'asc'|'desc'}
     */
    private function sorting(Request $request): array
    {
        $sort = (string) $request->query('sort', 'created_at');

        if ($sort !== 'platform' && ! array_key_exists($sort, self::SORT_COLUMNS)) {
            $sort = 'created_at';
        }

        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        return [$sort, $direction];
    }

    private function applySorting(Builder $query, string $sort, string $direction): void
    {
        if ($sort === 'platform') {
            $query
                ->leftJoin('platforms', 'platforms.id', '=', 'links.platform_id')
                ->orderBy('platforms.name', $direction);

            return;
        }

        $query->orderBy(self::SORT_COLUMNS[$sort], $direction);
    }
}
