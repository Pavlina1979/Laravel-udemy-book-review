<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
  use HasFactory;

  public function reviews()
  {
    return $this->hasMany(Review::class);
  }

  #[Scope]
  protected function title(Builder $query, string $title)
  {
    $query->where('title', 'LIKE', '%' . $title . '%');
  }

  #[Scope]
  protected function withReviewsCount(Builder $query, $from = null, $to = null)
  {
    $query->withCount([
      'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
    ]);
  }

  #[Scope]
  protected function withAvgRating(Builder $query, $from = null, $to = null)
  {
    $query->withAvg([
      'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
    ], 'rating');
  }

  #[Scope]
  protected function popular(Builder $query, $from = null, $to = null)
  {
    $query->withReviewsCount()->orderBy('reviews_count', 'desc');
  }

  #[Scope]
  protected function highestRated(Builder $query, $from = null, $to = null)
  {
    $query->withAvgRating()->orderBy('reviews_avg_rating', 'desc');
  }

  #[Scope]
  protected function minReviews(Builder $query, int $minReviews)
  {
    $query->having('reviews_count', '>=', $minReviews);
  }

  private function dateRangeFilter(Builder $query, $from = null, $to = null)
  {
    if ($from && !$to) {
      $query->where('created_at', '>=', $from);
    } elseif (!$from && $to) {
      $query->where('created_at', '<=', $to);
    } elseif ($from && $to) {
      $query->where('created_at', '>=', $from)->where('created_at', '<=', $to);
    }
  }

  #[Scope]
  protected function popularLastMonth(Builder $query)
  {
    $query->popular(now()->subMonth(), now())->highestRated(now()->subMonth());
  }

  #[Scope]
  protected function popularLast6Months(Builder $query)
  {
    $query->popular(now()->subMonths(6))->highestRated(now()->subMonths(6))->minReviews(5);
  }

  #[Scope]
  protected function highestRatedLastMonth(Builder $query)
  {
    $query->highestRated(now()->subMonth())->popular(now()->subMonth())->minReviews(2);
  }

  #[Scope]
  protected function highestRatedLast6Months(Builder $query)
  {
    $query->highestRated(now()->subMonths(6))->popular(now()->subMonths(6))->minReviews(5);
  }

  /**

     * The "booted" method of the model.

     */

  protected static function booted(): void
  {

    static::updated(fn(Book $book) => cache()->forget('book:' . $book->id));
    static::deleted(fn(Book $book) => cache()->forget('book:' . $book->id));

  }
}
