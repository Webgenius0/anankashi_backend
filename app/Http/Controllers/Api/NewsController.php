<?php

namespace App\Http\Controllers\api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Str;
use Throwable;

class NewsController extends Controller
{
    //
    public function news()
    {

        try {
            $news = News::all()->map(function ($news) {
                return [
                    'id' => $news->id,
                    'slug' => $news->slug,
                    'title' => $news->title,
                    'description' => Str::limit($news->short_description),
                    'thumbnail' => $news->thumbnail ? asset($news->thumbnail) : null,
                    'date' => $news->created_at->format('l F d Y'),

                ];
            });
            return response()->json([
                'status' => true,
                'message' => 'News fetched successfully',
                'data' => $news

            ]);
        } catch (ValidationException $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function news_details()
    {
        $slug = request('slug');

        try {
            $news = News::with('details.images')
                ->where('slug', $slug)
                ->first();

            if (!$news) {
                return Helper::jsonErrorResponse('News not found', 404);
            }
            $contentText = strip_tags($news->short_description ?? '');
            foreach ($news->details as $detail) {
                $contentText .= ' ' . strip_tags($detail->description ?? '');
            }
            $wordCount = str_word_count($contentText);
            $readingTime = max(1, ceil($wordCount / 200));
            $newsData = [
                'id' => $news->id,
                'title' => $news->title,
                'description' => $news->short_description,
                'date' => $news->created_at->format('F d Y'),
                'reading_time' => $readingTime . ' min read',

                'details' => $news->details->map(function ($detail) {
                    return [
                        'title' => $detail->title,
                        'description' => $detail->description,
                        'images' => $detail->images->map(function ($image) {
                            return [
                                'image' => $image->image ? asset($image->image) : null,
                            ];
                        }),
                    ];
                }),
                'total_comments' => $news->comments->count(),
                'total_likes' => $news->comments->count(),
                'comments' => $news->comments->whereNull('parent_id')->values()->map(function ($comment) {

                    return [

                        'id' => $comment->id,
                        'comment' => $comment->comment,
                        'user' => $comment->user ? [
                            'name' => $comment->user->name,
                            'avatar' => $comment->user->avatar ? asset($comment->user->avatar) : null,
                            'commented_at' => $comment->created_at->diffForHumans(),
                        ] : null,
                        'replies' => $comment->replies->map(function ($reply) {
                            return [
                                'id' => $reply->id,
                                'comment' => $reply->comment,
                                'user' => $reply->user ? [
                                    'name' => $reply->user->name,
                                    'avatar' => $reply->user->avatar ? asset($reply->user->avatar) : null,
                                    'commented_at' => $reply->created_at->diffForHumans(),
                                ] : null,
                            ];
                        })




                    ];
                }),
            ];

            return response()->json([
                'status' => true,
                'message' => 'News fetched successfully',
                'data' => $newsData
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }


    public function addComment(Request $request)
    {
        // dd($request->all());
        try {

            $request->validate([
                'news_id' => 'required|exists:news,id',
                'comment' => 'required|string|min:3'
            ]);

            $comment = Comment::create([
                'news_id' => $request->news_id,
                'user_id' => auth('api')->id(),
                'parent_id' => $request->parent_id ?? null,
                'comment' => $request->comment,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Comment added successfully',
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }

    public function toggleLike(Request $request)
    {
       try {
        $request->validate([
            'news_id' => 'required|exists:news,id',
        ]);

        $userId = auth('api')->id();

        $like = Like::where('news_id', $request->news_id)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create([
                'news_id' => $request->news_id,
                'user_id' => $userId
            ]);
            $liked = true;
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $liked ? 'News liked successfully' : 'News unliked successfully',
        ]);

        } catch (ValidationException $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse($e->errors(), 422, $e->getMessage());
        } catch (Throwable $e) {
            DB::rollBack();

            return Helper::jsonErrorResponse(
                config('app.debug') ? $e->getMessage() : 'Internal server error',
                500
            );
        }
    }
}
