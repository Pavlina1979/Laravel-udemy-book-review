@extends('layouts.app')

@section('content')
  <h1 class="mb-10 text-2xl">Books</h1>

  <form action="{{ route('books.index') }}" method="GET" class="mb-7 flex items-center gap-2">
    <input type="text" name="title" id="title" placeholder="Search by title" value="{{ request('title') }}" class="input">
    <input type="hidden" name="filter" value="{{ request('filter') }}">
    <button class="btn" type="submit">Search</button>
    <a class="btn" href="{{ route('books.index') }}">Clear</a>
  </form>


  <div class="filter-container mb-4 flex">
    @php
      $filters = [
        '' => 'Latest',
        'popular_last_month' => 'Popular last month',
        'popular_last_6months' => 'Popular last 6 months',
        'highest_rated_last_month' => 'Highest rated last month',
        'highest_rated_last_6months' => 'Highest rated last 6 months'
      ]
    @endphp

    @foreach ($filters as $key => $label)
      <a href="{{ route('books.index', [...request()->query(), 'filter' => $key]) }}"
        class="{{ request('filter') === $key || (request('filter') === null && $key === '') ? 'filter-item-active' : 'filter-item' }}">{{ $label }}</a>
    @endforeach
  </div>
  <ul>
    @forelse ($books as $book)


      <li class="mb-4">
        <div class="book-item">
          <div class="flex flex-wrap items-center justify-between">
            <div class="w-full grow sm:w-auto">
              <a href="{{ route('books.show', $book) }}" class="book-title">{{ $book->title }}</a>
              <span class="book-author">{{ $book->author }}</span>
            </div>
            <div>
              <div class="book-rating">
                <x-star-rating :rating="$book->reviews_avg_rating" />
              </div>
              <div class="book-review-count">
                out of {{ $book->reviews_count }} reviews
              </div>
            </div>
          </div>
        </div>
      </li>

    @empty
      <li class="mb-4">
        <div class="empty-book-item">
          <p class="empty-text">No books found</p>
          <a href="{{ route('books.index') }}" class="reset-link">Reset criteria</a>
        </div>
      </li>
    @endforelse
  </ul>
  {{ $books->links() }}
@endsection