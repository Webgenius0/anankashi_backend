<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $comments = Comment::with([
                'user',
                'news',
                'replies.user',
                'replies.replies.user'
            ])
                ->whereNull('parent_id')
                ->latest()
                ->select('id', 'user_id', 'news_id', 'comment', 'created_at')
                ->get();

            return DataTables::of($comments)
                ->addIndexColumn()
                ->addColumn('commenter', fn($comment) => $comment->user?->name ?? 'Guest')
                ->addColumn('post',      fn($comment) => $comment->news?->title ?? '—')
                ->addColumn('comment',   fn($comment) => $this->buildCommentThreadHtml($comment))
                ->addColumn('action',    fn($comment) => $this->buildActionButtons($comment))
                ->rawColumns(['comment', 'action'])
                ->with('stats', ['total' => Comment::whereNull('parent_id')->count()])
                ->make(true);
        }

        return view('backend.layouts.comment.index');
    }

    /**
     * Avatar background color — consistent with the JS side.
     */
    private function avatarColor(string $name): string
    {
        $palette = ['#e8490a', '#065fd4', '#7c3aed', '#059669', '#d97706', '#db2777'];
        return $palette[ord($name) % count($palette)];
    }

    /**
     * Admin badge HTML.
     */
    private function adminBadge(): string
    {
        return '<span class="admin-badge">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Admin
        </span>';
    }

    /**
     * Build threaded comment HTML (up to 3 levels) — YouTube style.
     * Replies are hidden by default; clicking the toggle reveals them.
     */
    private function buildCommentThreadHtml(Comment $comment): string
    {
        $text    = e($comment->comment);
        $time    = $comment->created_at->diffForHumans();
        $replies = $comment->replies ?? collect();
        $count   = $replies->count();

        // ── Build Level 1 + Level 2 reply rows ──────────────────
        $replyRowsHtml = '';

        foreach ($replies as $reply) {
            $rName    = $reply->user?->name ?? 'Guest';
            $rText    = e($reply->comment);
            $rTime    = $reply->created_at->diffForHumans();
            $rId      = $reply->id;
            $rNewsId  = $reply->news_id;
            $isAdmin  = $reply->user?->hasRole('admin') ?? false;

            $rBg      = $isAdmin ? '#065fd4' : $this->avatarColor($rName);
            $rAvCls   = $isAdmin ? 'reply-avatar is-admin' : 'reply-avatar';
            $rBubCls  = $isAdmin ? 'reply-bubble is-admin' : 'reply-bubble';
            $rBadge   = $isAdmin ? $this->adminBadge() : '';

            // ── Level 2 sub-replies ──────────────────────────────
            $subHtml = '';
            $subs    = $reply->replies ?? collect();

            if ($subs->count()) {
                $subHtml .= '<div class="subreply-list">';
                foreach ($subs as $sub) {
                    $sName   = $sub->user?->name ?? 'Guest';
                    $sText   = e($sub->comment);
                    $sTime   = $sub->created_at->diffForHumans();
                    $sId     = $sub->id;
                    $sAdmin  = $sub->user?->hasRole('admin') ?? false;
                    $sBg     = $sAdmin ? '#065fd4' : $this->avatarColor($sName);
                    $sAvCls  = $sAdmin ? 'reply-avatar is-admin' : 'reply-avatar';
                    $sBubCls = $sAdmin ? 'reply-bubble is-admin' : 'reply-bubble';
                    $sBadge  = $sAdmin ? $this->adminBadge() : '';
                    $sInit   = strtoupper(substr($sName, 0, 1));

                    $subHtml .= <<<HTML
                    <div class="reply-row">
                      <div class="{$sAvCls}" style="background:{$sBg}">{$sInit}</div>
                      <div class="reply-body">
                        <div class="{$sBubCls}">
                          <div class="reply-header">
                            <span class="reply-author-name">{$sName} {$sBadge}</span>
                            <span class="reply-time">{$sTime}</span>
                          </div>
                          <div class="reply-text">{$sText}</div>
                        </div>
                        <div class="reply-actions">
                          <button class="btn-sm edit-sm edit-reply" data-id="{$sId}">
                            <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                          </button>
                          <button class="btn-sm delete-sm delete-reply" data-id="{$sId}">
                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
                            Delete
                          </button>
                        </div>
                      </div>
                    </div>
                    HTML;
                }
                $subHtml .= '</div>';
            }

            $rInit = strtoupper(substr($rName, 0, 1));

            $replyRowsHtml .= <<<HTML
            <div class="reply-row">
              <div class="{$rAvCls}" style="background:{$rBg}">{$rInit}</div>
              <div class="reply-body">
                <div class="{$rBubCls}">
                  <div class="reply-header">
                    <span class="reply-author-name">{$rName} {$rBadge}</span>
                    <span class="reply-time">{$rTime}</span>
                  </div>
                  <div class="reply-text">{$rText}</div>
                  {$subHtml}
                </div>
                <div class="reply-actions">
                  <button class="btn-sm reply-act reply-comment"
                          data-id="{$rId}"
                          data-news-id="{$rNewsId}"
                          data-author="{$rName}">
                    <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Reply
                  </button>
                  <button class="btn-sm edit-sm edit-reply" data-id="{$rId}">
                    <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit
                  </button>
                  <button class="btn-sm delete-sm delete-reply" data-id="{$rId}">
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
                    Delete
                  </button>
                </div>
              </div>
            </div>
            HTML;
        }

        // ── Toggle button + collapsible reply list ───────────────
        $replySection = '';
        if ($count > 0) {
            $label = $count === 1 ? 'reply' : 'replies';
            $replySection = <<<HTML
            <button class="toggle-replies-btn" data-count="{$count}">
              <svg class="chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
              <span class="reply-label">{$count} {$label}</span>
            </button>
            <div class="reply-list">
              {$replyRowsHtml}
            </div>
            HTML;
        }

        return <<<HTML
        <div class="thread-cell">
          <div class="comment-text">{$text}</div>
          <div class="comment-time">{$time}</div>
          {$replySection}
        </div>
        HTML;
    }

    /**
     * Action buttons for the top-level comment row.
     */
    private function buildActionButtons(Comment $comment): string
    {
        $id     = $comment->id;
        $newsId = $comment->news_id;
        $name   = e($comment->user?->name ?? 'Guest');

        return <<<HTML
        <div class="action-group">
          <button class="btn-act reply-act reply-comment"
                  data-id="{$id}"
                  data-news-id="{$newsId}"
                  data-author="{$name}">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Reply
          </button>
          <button class="btn-act edit-act edit-comment" data-id="{$id}">
            <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit
          </button>
          <button class="btn-act delete-act delete-comment" data-id="{$id}">
            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
            Delete
          </button>
        </div>
        HTML;
    }

    /**
     * Store a new reply.
     */
    public function store(Request $request)
    {
        $request->validate([
            'news_id'   => 'required|exists:news,id',
            'comment'   => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'user_id'   => auth()->id(),
            'news_id'   => $request->news_id,
            'parent_id' => $request->parent_id,
            'comment'   => $request->comment,
        ]);

        return response()->json([
            'message' => 'Reply added successfully',
            'comment' => $comment,
        ]);
    }

    /**
     * Return a comment for editing.
     */
    public function edit($id)
    {
        $comment = Comment::findOrFail($id);
        return response()->json($comment);
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = Comment::findOrFail($id);
        $comment->update(['comment' => $request->comment]);

        return response()->json(['message' => 'Comment updated successfully']);
    }

    /**
     * Delete a comment (cascades to replies via model/DB).
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
