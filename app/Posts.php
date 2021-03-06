<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class Posts extends Model implements SluggableInterface {

	use SluggableTrait;

	protected $sluggable = [
		'build_from' => 'title',
		'save_to' => 'slug',
	];

	protected $fillable = [
		'title',
		'body',
		'published_at',
		'board'
	];

	protected $dates = ['published_at'];

	public function scopePublished($query)
	{
		$query->where('published_at', '<=', Carbon::now());
	}

	public function scopeUnpublished($query)
	{
		$query->where('published_at', '>', Carbon::now());
	}

	public function scopeBoard($query, $board)
	{
		$query->where('board', '=', $board);
	}

	public function setPublishedAtAttribute($date)
	{
		$this->attributes['published_at'] = Carbon::createFromFormat('Y-m-d', $date);
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function comments()
	{
		return $this->hasMany('App\Comments');
	}

	public function postvotes()
	{
		return $this->hasMany('App\PostVotes');
	}

	public function commentvotes()
	{
		return $this->hasMany('App\CommentVotes');
	}

}
