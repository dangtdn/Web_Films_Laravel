<?php

namespace App\Http\Controllers\Dashboard;

use App\Actor;
use App\Category;
use App\Film;
use App\Http\Controllers\Controller;
use App\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FilmController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:create_films,guard:admin'])->only(['create', 'store']);
        $this->middleware(['permission:read_films,guard:admin'])->only('index');
        $this->middleware(['permission:update_films,guard:admin'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_films,guard:admin'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $films = Film::where(function ($query) use ($request) {
            $query->when($request->search, function ($q) use ($request) {
                return $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('year', 'like', '%' . $request->search . '%');
            });
            $query->when($request->category, function ($q) use ($request) {
                return $q->whereHas('categories', function ($q2) use ($request){
                    return $q2->whereIn('category_id', (array)$request->category)
                        ->orWhereIn('name', (array)$request->category);
                });
            });
            $query->when($request->actor, function ($q) use ($request) {
                return $q->whereHas('actors', function ($q2) use ($request){
                    return $q2->whereIn('actor_id', (array)$request->actor)
                        ->orWhereIn('name', (array)$request->actor);
                });
            });
            $query->when($request->favorite, function ($q) use ($request) {
                return $q->whereHas('favorites', function ($q2) use ($request){
                    return $q2->whereIn('user_id', (array)$request->favorite);
                });
            });
        })->with('categories')->with('ratings')->latest()->paginate(10);
        $categories = Category::all();
        $actors = Actor::all();

        return view('dashboard.films.index', compact('films', 'categories', 'actors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categories = Category::all();
        $actors = Actor::all();
        $memberships = Membership::all();

        return view('dashboard.films.create', compact('categories', 'actors', 'memberships'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $attributes = $request->validate([
            'name' => 'required|string|max:50|min:1|unique:films',
            'year' => 'required|string|min:4',
            'overview' => 'required|string',
            'background_cover' => 'required|image',
            'poster' => 'required|image',
            'url' => 'required|array|min:1',
            'api_url' => 'required|array|min:1',
            'type_film' => 'required',
            'categories' => 'required|array|max:3|exists:categories,id',
            'actors' => 'required|array|max:10|exists:actors,id',
            'is_free' => 'required|boolean',
            'memberships_can_see' => 'required|array|max:10|exists:memberships,id'
        ]);

        $attributes['background_cover'] = $request->background_cover->store('film_background_covers');
        $attributes['poster'] = $request->poster->store('film_posters');

        $film = Film::create([
            'name' => $attributes['name'],
            'year' => $attributes['year'],
            'overview' => $attributes['overview'],
            'background_cover' => $attributes['background_cover'],
            'poster' => $attributes['poster'],
            'url' => $attributes['url'],
            'api_url' => $attributes['api_url'],
            'is_free' => $attributes['is_free'],
            'memberships_can_see' => implode(',', $attributes['memberships_can_see'])
        ]);
        $film->categories()->sync($attributes['categories']);
        $film->actors()->sync($attributes['actors']);

        $id = $film->id;
        $episodes = array_map(function($a, $b) use($id) {
            return [
                'url' => $a ? $a : '', 
                'api_url' => $b ? $b : '', 
                'film_id' => $id
            ];
        }, $attributes['url'], $attributes['api_url']);

        $film->episodes()->insert($episodes);
        dd("Hello");

        session()->flash('success', 'Phim th??m th??nh c??ng');
        return redirect()->route('dashboard.films.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Film  $film
     * @return \Illuminate\Http\Response
     */
    public function show(Film $film)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Film  $film
     * @return \Illuminate\Http\Response
     */
    public function edit(Film $film)
    {
        //
        $categories = Category::all();
        $actors = Actor::all();
        $memberships = Membership::all();
        $memberships_can_see = explode(',', $film->memberships_can_see);
        // dd($memberships_can_see);
        return view('dashboard.films.edit', compact('film', 'categories', 'actors', 'memberships', 'memberships_can_see'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Film  $film
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Film $film)
    {
        //
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:50', 'min:1', Rule::unique('films')->ignore($film)],
            'year' => 'required|string|min:4',
            'overview' => 'required|string',
            'background_cover' => 'nullable|image',
            'poster' => 'nullable|image',
            'url' => 'required|array|min:1',
            'api_url' => 'required|array|min:1',
            'type_film' => 'required',
            'categories' => 'required|array|max:3|exists:categories,id',
            'actors' => 'required|array|max:10|exists:actors,id',
            'is_free' => 'required|boolean',
            'memberships_can_see' => 'required|array|max:10|exists:memberships,id'
        ]);

        // dd($attributes['memberships_can_see']);
        $attributes['memberships_can_see'] = implode(',', $attributes['memberships_can_see']);
        // dd($attributes);
        if ($request->background_cover) {
            Storage::delete($film->getAttributes()['background_cover']);
            $attributes['background_cover'] = $request->background_cover->store('film_background_covers');
        }
        if ($request->poster) {
            Storage::delete($film->getAttributes()['poster']);
            $attributes['poster'] = $request->poster->store('film_posters');
        }

        $film->update($attributes);
        $film->categories()->sync($attributes['categories']);
        $film->actors()->sync($attributes['actors']);

        $id = $film->id;
        $episodes = array_map(function($a, $b) use($id) {
            return [
                'url' => $a ? $a : '', 
                'api_url' => $b ? $b : '', 
                'film_id' => $id
            ];
        }, $attributes['url'], $attributes['api_url']);

        $film->episodes()->delete();
        $film->episodes()->insert($episodes);

        session()->flash('success', 'Phim c???p nh???t th??nh c??ng');
        return redirect()->route('dashboard.films.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Film $film
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Film $film)
    {
        //

        $film->delete();

        session()->flash('success', 'Phim xo?? th??nh c??ng');
        return redirect()->route('dashboard.films.index');
    }
}
