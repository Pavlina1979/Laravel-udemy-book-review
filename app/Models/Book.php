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
  protected function popular(Builder $query, $from = null, $to = null)
  {
    $query->withCount([
      'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
    ])->orderBy('reviews_count', 'desc');
  }

  #[Scope]
  protected function highestRated(Builder $query, $from = null, $to = null)
  {
    $query->withAvg([
      'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
    ], 'rating')->orderBy('reviews_avg_rating', 'desc');
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
      $query->where('created_at', [$from, $to]);
    }
  }
}
