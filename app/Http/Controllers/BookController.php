<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $title = $request->input('title');
    $filter = $request->query('filter', '');

    $books = Book::when(
      $title,
      fn($query, string $title) =>
      $query->title($title)
    );
    $books = match ($filter) {
      'popular_last_month' => $books->popularLastMonth(),
      'popular_last_6months' => $books->popularLast6Months(),
      'highest_rated_last_month' => $books->highestRatedLastMonth(),
      'highest_rated_last_6months' => $books->highestRatedLast6Months(),
      default => $books->latest()->withAvgRating()->withReviewsCount()
    };
    //$books = $books->get();

    $cacheKey = 'books:' . $filter . ':' . $title;
    $books = $books->paginate(15);

    // pro zapnutí cachování:
    //$books = Cache::remember($cacheKey, 3600, fn() => $books->paginate(15));

    return view('books.index', [
      'books' => $books
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    // $book = Book::findOrFail($id);
    //$book = Book::load()->withAvgRating()->withReviewsCount()->findOrFail($id);

    $book = Book::with([
      'reviews' => fn($query) => $query->orderBy('created_at', 'DESC')->paginate(5)
    ])->withAvgRating()->withReviewsCount()->findOrFail($id);

    $cacheKey = 'book:' . $id;

    // $book = cache()->remember(
    //   $cacheKey,
    //   3600,
    //   fn() => Book::with([
    //     'reviews' => fn($query) => $query->paginate(5)
    //   ])->withAvgRating()->withReviewsCount()->findOrFail($id)
    // );
    $reviews = $book->reviews()->paginate(5);

    return view('books.show', [
      'book' => $book,
      'reviews' => $reviews
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
