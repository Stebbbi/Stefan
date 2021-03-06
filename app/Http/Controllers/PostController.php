<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Request;
use App\Http\Requests\PostRequest;
use App\Posts;
use App\Comments;
use Carbon\Carbon;
use Auth;
use App\PostVotes;
use App\CommentVotes;
use App\User;

class PostController extends Controller {

	function __construct() {
		$this->middleware('auth', ['only' => ['create', 'upvote', 'downvote']]);
	}

	public function show($slug)
	{
		$post = Posts::whereSlug($slug)->first();

		$post->views += 1;
		$post->save();

		$comments = $post->comments()->orderBy('published_at', 'asc')->get();

		return view('forum.view', compact('post', 'comments'));
	}

	public function store(PostRequest $request)
	{
		$post = new Posts($request->all());

		Auth::user()->posts()->save($post);

		return redirect('');
	}

	public function update($post, PostRequest $request)
	{
		$post = Posts::whereSlug($post)->firstOrFail();

		$post->update($request->all());

		return redirect('post/' . $post->slug);
	}

	public function edit($post)
	{
		$post = Posts::whereSlug($post)->firstOrFail();
		return view('forum.edit', compact('post'));
	}


	public function create()
	{
		return view('forum.create');
	}

	public function upvote($post)
	{
		$posts = Posts::whereSlug($post)->firstOrFail();
		$user = $posts->user()->first();

		$existing_vote = PostVotes::wherePostsId($posts->id)->whereUserId(Auth::id())->first();

		if(!is_null($existing_vote)) {
			$existing_vote->vote = "up";
			$existing_vote->save();

			$userUpvote = $posts->postvotes()->where('user_id', $user->id)->where('vote', 'up')->count();
			$userDownvote = $posts->postvotes()->where('user_id', $user->id)->where('vote', 'down')->count();

			$userVote = $userUpvote - $userDownvote;

			$postUpvote = $posts->postvotes()->whereVote("up")->count();
			$postDownvote = $posts->postvotes()->whereVote("down")->count();

			$postVote = $postUpvote - $postDownvote;

			$posts->votes = $postVote;
			$posts->save();
			$user->votes = $userVote;
			$user->save();

			return redirect('post/' . $posts->slug);
		} else {
			$vote = new PostVotes();
			$vote->user()->associate(Auth::user());
			$vote->posts()->associate($posts);
			$vote->vote = "up";
			$vote->save();

			$posts->votes += 1;
			$posts->save();
			$user->votes += 1;
			$user->save();

			return redirect('post/' . $posts->slug);
		}
	}

	public function downvote($post)
	{
		$posts = Posts::whereSlug($post)->firstOrFail();
		$user = $posts->user()->first	();

		$existing_vote = PostVotes::wherePostsId($posts->id)->whereUserId(Auth::id())->first();

		if(!is_null($existing_vote)) {
			$existing_vote->vote = "down";
			$existing_vote->save();

			$userUpvote = $posts->postvotes()->where('user_id', $user->id)->where('vote', 'up')->count();
			$userDownvote = $posts->postvotes()->where('user_id', $user->id)->where('vote', 'down')->count();

			$userVote = $userUpvote - $userDownvote;

			$postUpvote = $posts->postvotes()->whereVote("up")->count();
			$postDownvote = $posts->postvotes()->whereVote("down")->count();

			$postVote = $postUpvote - $postDownvote;

			$posts->votes = $postVote;
			$posts->save();
			$user->votes = $userVote;
			$user->save();

			return redirect('post/' . $posts->slug);
		} else {
			$vote = new PostVotes();
			$vote->user()->associate(Auth::user());
			$vote->posts()->associate($posts);
			$vote->vote = "down";
			$vote->save();

			$posts->votes -= 1;
			$user->votes -= 1;
			$posts->save();
			$user->save();

			return redirect('post/' . $posts->slug);
		}
	}

	public function destroy($post)
	{
		Posts::whereSlug($post)->delete();
		return redirect('');
	}

}
