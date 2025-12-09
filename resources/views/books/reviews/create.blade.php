@extends('layouts.app')

@section('content')
  <h1 class="mb-10 text-2xl">Add review for {{ $book->title }}</h1>

  <form action="{{ route('books.reviews.store', $book) }}" method="post">
    @csrf
    <label for="review">Review</label>
    <textarea name="review" id="review" class="input mb-4"></textarea>
    @error('review')
      <p class="error mb-5">{{ $message }}</p>
    @enderror

    <label for="rating"></label>
    <select name="rating" id="rating" class="input mb-4">
      <option value="">Select a rating</option>
      @for ($i = 1; $i <= 5; $i++)
        <option value="{{ $i }}">{{ $i }}</option>

      @endfor
    </select>
    @error('rating')
      <p class="error mb-5">{{ $message }}</p>
    @enderror

    <button type="submit" class="btn">Add review</button>
  </form>
@endsection