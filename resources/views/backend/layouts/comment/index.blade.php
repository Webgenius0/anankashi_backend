@extends('backend.app', ['title' => 'Comments'])

@push('styles')
<link href="{{ asset('default/datatable.css') }}" rel="stylesheet" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════════════════
   VARIABLES
══════════════════════════════════════════════════ */
:root {
  --ink:        #0f0f0f;
  --ink2:       #606060;
  --ink3:       #909090;
  --line:       rgba(0,0,0,0.07);
  --line-med:   rgba(0,0,0,0.12);
  --bg:         #f7f7f7;
  --surf:       #ffffff;
  --surf2:      #f2f2f2;
  --accent:     #e8490a;
  --blue:       #065fd4;
  --blue-bg:    #e8f0fe;
  --admin-bg:   #f2f2f2;
  --r-sm:       6px;
  --r-md:       10px;
  --r-lg:       14px;
  --sans:       'Inter', system-ui, sans-serif;
  --t:          150ms ease;
}

/* ══════════════════════════════════════════════════
   RESET / BASE
══════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }
body { font-family: var(--sans) !important; background: var(--bg) !important; color: var(--ink); }

/* ══════════════════════════════════════════════════
   PAGE HEADER
══════════════════════════════════════════════════ */
.page-header {
  padding: 28px 0 16px;
  border-bottom: none;
  margin-bottom: 4px;
}
.page-title {
  font-size: 22px !important;
  font-weight: 600 !important;
  color: var(--ink) !important;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
  letter-spacing: -0.3px;
}
.count-badge {
  background: var(--accent);
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  border-radius: 20px;
  padding: 3px 9px;
  letter-spacing: 0;
}
.breadcrumb { margin-bottom: 8px; background: none; padding: 0; }
.breadcrumb-item a { color: var(--ink3); font-size: 12px; text-decoration: none; }
.breadcrumb-item a:hover { color: var(--ink); }
.breadcrumb-item.active { color: var(--ink3); font-size: 12px; }
.breadcrumb-item + .breadcrumb-item::before { color: var(--ink3); opacity: .4; }

/* ══════════════════════════════════════════════════
   TOOLBAR
══════════════════════════════════════════════════ */
.comments-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
  gap: 10px;
}
.toolbar-left {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}
.filter-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 6px 14px;
  border-radius: 20px;
  border: 1px solid var(--line-med);
  background: var(--surf);
  font-family: var(--sans);
  font-size: 12px;
  font-weight: 500;
  color: var(--ink2);
  cursor: pointer;
  transition: all var(--t);
  white-space: nowrap;
}
.filter-pill:hover  { background: var(--surf2); color: var(--ink); }
.filter-pill.active { background: var(--ink); color: #fff; border-color: var(--ink); }
.filter-pill svg    { width: 12px; height: 12px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }

.sort-btn {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-family: var(--sans);
  font-size: 12px;
  color: var(--ink2);
  background: var(--surf);
  border: 1px solid var(--line-med);
  border-radius: 20px;
  padding: 6px 14px;
  cursor: pointer;
  transition: all var(--t);
  white-space: nowrap;
}
.sort-btn:hover { color: var(--ink); border-color: var(--ink3); }
.sort-btn svg { width: 12px; height: 12px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }

/* ══════════════════════════════════════════════════
   CARD WRAPPER
══════════════════════════════════════════════════ */
.card {
  background: var(--surf) !important;
  border: 1px solid var(--line) !important;
  border-radius: var(--r-lg) !important;
  box-shadow: none !important;
  overflow: hidden;
}
.card-body { padding: 0 !important; }

/* ══════════════════════════════════════════════════
   DATATABLE OVERRIDES
══════════════════════════════════════════════════ */
#datatable { font-family: var(--sans) !important; border-collapse: collapse !important; }

#datatable thead tr { border-bottom: 1px solid var(--line) !important; background: var(--bg) !important; }
#datatable thead th {
  font-size: 10px !important;
  font-weight: 600 !important;
  letter-spacing: .7px !important;
  text-transform: uppercase !important;
  color: var(--ink3) !important;
  padding: 11px 18px !important;
  border: none !important;
  white-space: nowrap;
}

#datatable tbody tr { border-bottom: 1px solid var(--line) !important; transition: background var(--t); }
#datatable tbody tr:last-child { border-bottom: none !important; }
#datatable tbody tr:hover { background: #fafafa !important; }
#datatable tbody td { padding: 14px 18px !important; border: none !important; vertical-align: top; }

/* DataTable controls */
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
  border: 1px solid var(--line-med) !important;
  border-radius: 20px !important;
  padding: 6px 14px !important;
  font-family: var(--sans) !important;
  font-size: 12px !important;
  color: var(--ink) !important;
  background: var(--surf) !important;
  outline: none;
}
.dataTables_wrapper .dataTables_filter input:focus { border-color: var(--ink) !important; box-shadow: none !important; }
.dataTables_wrapper .dataTables_info { font-size: 12px !important; color: var(--ink3) !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button {
  border-radius: 8px !important;
  font-family: var(--sans) !important;
  font-size: 12px !important;
  padding: 4px 10px !important;
  color: var(--ink2) !important;
  border: 1px solid var(--line-med) !important;
  background: none !important;
  transition: all var(--t) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: var(--bg) !important; color: var(--ink) !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
  background: var(--ink) !important;
  color: #fff !important;
  border-color: var(--ink) !important;
}

/* ══════════════════════════════════════════════════
   ROW INDEX
══════════════════════════════════════════════════ */
.row-index { font-size: 12px; color: var(--ink3); font-variant-numeric: tabular-nums; }

/* ══════════════════════════════════════════════════
   POST CELL
══════════════════════════════════════════════════ */
.post-cell { max-width: 160px; }
.post-title-link {
  font-size: 12px;
  font-weight: 500;
  color: var(--ink);
  text-decoration: none;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.45;
  transition: color var(--t);
}
.post-title-link:hover { color: var(--accent); }

/* ══════════════════════════════════════════════════
   COMMENTER CELL
══════════════════════════════════════════════════ */
.commenter-cell { display: flex; align-items: center; gap: 10px; min-width: 130px; }
.commenter-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 600;
  color: #fff;
  flex-shrink: 0;
}
.commenter-name { font-size: 13px; font-weight: 500; color: var(--ink); line-height: 1.3; }
.commenter-role { font-size: 10px; color: var(--ink3); margin-top: 1px; }

/* ══════════════════════════════════════════════════
   THREAD CELL
══════════════════════════════════════════════════ */
.thread-cell { max-width: 480px; }

.comment-text { font-size: 14px; color: var(--ink); line-height: 1.6; }
.comment-time { font-size: 11px; color: var(--ink3); margin-top: 4px; }

/* ── Toggle replies button ── */
.toggle-replies-btn {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  font-family: var(--sans);
  font-size: 13px;
  font-weight: 600;
  color: var(--blue);
  padding: 7px 10px 7px 0;
  margin-top: 8px;
  transition: color var(--t);
  user-select: none;
}
.toggle-replies-btn:hover { color: #1a73e8; }
.toggle-replies-btn .chev {
  width: 18px;
  height: 18px;
  stroke: currentColor;
  fill: none;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
  transition: transform .22s ease;
  display: block;
  flex-shrink: 0;
}
.toggle-replies-btn.open .chev { transform: rotate(180deg); }

/* ── Reply list ── */
.reply-list {
  display: none;
  flex-direction: column;
  gap: 14px;
  margin-top: 10px;
  padding-top: 12px;
  border-top: 1px solid var(--line);
  animation: slideDown .22s ease;
}
.reply-list.visible { display: flex; }

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-6px); }
  to   { opacity: 1; transform: translateY(0); }
}

.reply-row { display: flex; gap: 10px; align-items: flex-start; }
.reply-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 600;
  color: #fff;
  flex-shrink: 0;
  margin-top: 1px;
}
.reply-avatar.is-admin { background: #065fd4 !important; }

.reply-body { flex: 1; min-width: 0; }
.reply-bubble {
  border-radius: var(--r-md);
  padding: 9px 12px;
  background: var(--surf2);
}
.reply-bubble.is-admin { background: var(--admin-bg); }

.reply-header {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
  margin-bottom: 3px;
}
.reply-author-name {
  font-size: 12px;
  font-weight: 600;
  color: var(--ink);
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.reply-time { font-size: 11px; color: var(--ink3); }
.reply-text { font-size: 13px; color: var(--ink); line-height: 1.55; }

.admin-badge {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  background: var(--blue-bg);
  color: var(--blue);
  font-size: 10px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 20px;
}
.admin-badge svg {
  width: 9px; height: 9px;
  stroke: currentColor; fill: none;
  stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
}

/* ── Reply mini-actions ── */
.reply-actions { display: flex; gap: 2px; margin-top: 5px; }
.btn-sm {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  font-family: var(--sans);
  font-size: 11px;
  padding: 3px 8px;
  border-radius: 14px;
  border: none;
  cursor: pointer;
  background: none;
  color: var(--ink3);
  transition: all var(--t);
}
.btn-sm svg { width: 10px; height: 10px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
.btn-sm:hover             { background: var(--surf2); color: var(--ink2); }
.btn-sm.edit-sm:hover     { color: #b45309; background: #fefce8; }
.btn-sm.delete-sm:hover   { color: #b91c1c; background: #fef2f2; }

/* ── Sub-replies ── */
.subreply-list {
  margin-top: 10px;
  padding-left: 14px;
  border-left: 2px solid var(--line-med);
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* ══════════════════════════════════════════════════
   ACTIONS COLUMN
══════════════════════════════════════════════════ */
.action-group { display: flex; flex-direction: column; gap: 3px; min-width: 90px; }
.btn-act {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-family: var(--sans);
  font-size: 12px;
  padding: 5px 10px;
  border-radius: 20px;
  border: 1px solid transparent;
  cursor: pointer;
  background: none;
  color: var(--ink2);
  transition: all var(--t);
  white-space: nowrap;
  text-align: left;
  line-height: 1;
}
.btn-act svg { width: 12px; height: 12px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
.btn-act:hover              { background: var(--surf2); color: var(--ink); }
.btn-act.reply-act:hover    { color: var(--blue);  background: var(--blue-bg); }
.btn-act.edit-act:hover     { color: #b45309;      background: #fefce8; }
.btn-act.delete-act:hover   { color: #b91c1c;      background: #fef2f2; }

/* ══════════════════════════════════════════════════
   MODALS
══════════════════════════════════════════════════ */
.modal-backdrop.show { opacity: .25; }
.modal-dialog { max-width: 480px; }
.modal-content {
  border: 1px solid var(--line-med) !important;
  border-radius: var(--r-lg) !important;
  box-shadow: 0 20px 60px rgba(0,0,0,.14) !important;
  overflow: hidden;
  font-family: var(--sans);
}
.modal-header {
  padding: 18px 22px 14px !important;
  border-bottom: 1px solid var(--line) !important;
  background: var(--surf);
}
.modal-title { font-size: 15px !important; font-weight: 600 !important; color: var(--ink) !important; letter-spacing: -0.2px; }
.modal-body { padding: 18px 22px !important; background: var(--bg); }
.modal-footer { padding: 12px 22px !important; border-top: 1px solid var(--line) !important; background: var(--surf); gap: 8px; }

.modal-label {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: .6px;
  text-transform: uppercase;
  color: var(--ink3);
  margin-bottom: 8px;
  display: block;
}
.modal-body .form-control {
  border: 1px solid var(--line-med) !important;
  border-radius: var(--r-sm) !important;
  padding: 10px 12px !important;
  font-family: var(--sans) !important;
  font-size: 14px !important;
  font-weight: 400 !important;
  color: var(--ink) !important;
  background: var(--surf) !important;
  line-height: 1.6 !important;
  resize: vertical !important;
  outline: none !important;
  transition: border-color var(--t), box-shadow var(--t) !important;
}
.modal-body .form-control:focus {
  border-color: var(--ink) !important;
  box-shadow: 0 0 0 3px rgba(15,15,15,.06) !important;
}
.modal-body .form-control::placeholder { color: var(--ink3) !important; }

.btn-modal-primary {
  background: var(--ink);
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 8px 20px;
  font-family: var(--sans);
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: opacity var(--t);
}
.btn-modal-primary:hover { opacity: .82; }
.btn-modal-secondary {
  background: transparent;
  color: var(--ink2);
  border: 1px solid var(--line-med);
  border-radius: 20px;
  padding: 8px 16px;
  font-family: var(--sans);
  font-size: 13px;
  cursor: pointer;
  transition: all var(--t);
}
.btn-modal-secondary:hover { color: var(--ink); border-color: #aaa; background: var(--bg); }
.btn-close { opacity: .35; transition: opacity var(--t); }
.btn-close:hover { opacity: .7; }

/* ══════════════════════════════════════════════════
   EMPTY STATE
══════════════════════════════════════════════════ */
.dt-empty {
  padding: 56px 24px;
  text-align: center;
  font-size: 14px;
  color: var(--ink3);
}
</style>
@endpush

@section('content')
<div class="app-content main-content mt-0">
  <div class="side-app">
    <div class="main-container container-fluid">

      {{-- PAGE HEADER --}}
      <div class="page-header">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Comments</li>
        </ol>
        <h1 class="page-title">
          Comments
        </h1>
      </div>

      {{-- TOOLBAR --}}


      {{-- TABLE --}}
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table id="datatable" class="table w-100">
                  <thead>
                    <tr>
                      <th style="width:40px">#</th>
                      <th style="width:160px">Post</th>
                      <th style="width:160px">Commenter</th>
                      <th>Comment &amp; Replies</th>
                      <th style="width:100px">Actions</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- ── REPLY MODAL ── --}}
<div class="modal fade" id="commentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="commentForm">
        <div class="modal-header">
          <h5 class="modal-title" id="commentModalLabel">Add Reply</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="parent_id" id="parent_id">
          <input type="hidden" name="news_id"   id="news_id">
          <label class="modal-label" for="comment_text">Your reply</label>
          <textarea name="comment" id="comment_text" class="form-control" rows="5" placeholder="Write your reply…"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-modal-primary">Post Reply</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ── EDIT MODAL ── --}}
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="editCommentForm">
        <div class="modal-header">
          <h5 class="modal-title">Edit Comment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="comment_id" id="edit_comment_id">
          <label class="modal-label" for="edit_comment_text">Edit content</label>
          <textarea name="comment" id="edit_comment_text" class="form-control" rows="5"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-modal-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

  /* ── DataTable ── */
  let table = $('#datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('admin.comment.index') }}",
      dataSrc: function (json) {
        if (json.stats) $('#stat-total').text(json.stats.total ?? '—');
        return json.data;
      }
    },
    columns: [
      {
        data: 'DT_RowIndex', name: 'DT_RowIndex',
        orderable: false, searchable: false,
        render: d => `<span class="row-index">${d}</span>`
      },
      {
        data: 'post', name: 'post',
        render: d => `<div class="post-cell"><a href="#" class="post-title-link">${d}</a></div>`
      },
      {
        data: 'commenter', name: 'commenter',
        render: d => {
          const colors = ['#e8490a','#065fd4','#7c3aed','#059669','#d97706','#db2777'];
          const bg     = colors[(d || '').charCodeAt(0) % colors.length];
          const init   = (d && d.length) ? d[0].toUpperCase() : '?';
          return `<div class="commenter-cell">
            <div class="commenter-avatar" style="background:${bg}">${init}</div>
            <div>
              <div class="commenter-name">${d ?? '—'}</div>
              <div class="commenter-role">Visitor</div>
            </div>
          </div>`;
        }
      },
      { data: 'comment', name: 'comment', orderable: false, searchable: true,  render: d => d },
      { data: 'action',  name: 'action',  orderable: false, searchable: false, render: d => d }
    ],
    language: {
      processing: `<span style="font-family:'Inter',sans-serif;font-size:13px;color:#909090">Loading…</span>`,
      emptyTable:  `<div class="dt-empty">No comments yet.</div>`
    },
    dom: "<'row align-items-center mb-3 px-3 pt-3'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'f>>" +
         "<'row'<'col-12'tr>>" +
         "<'row align-items-center mt-2 px-3 pb-3'<'col-sm-5'i><'col-sm-7 d-flex justify-content-end'p>>",
    pageLength: 15,
    lengthMenu: [10, 15, 25, 50]
  });

  /* ── Toggle reply list (YouTube-style) ── */
  $(document).on('click', '.toggle-replies-btn', function () {
    const $btn  = $(this);
    const $list = $btn.next('.reply-list');
    const count = parseInt($btn.data('count'), 10);
    const isOpen = $btn.hasClass('open');

    if (isOpen) {
      $list.removeClass('visible');
      $btn.removeClass('open');
      $btn.find('.reply-label').text(`${count} ${count === 1 ? 'reply' : 'replies'}`);
    } else {
      $list.addClass('visible');
      $btn.addClass('open');
      $btn.find('.reply-label').text('Hide replies');
    }
  });

  /* ── Open reply modal ── */
  $(document).on('click', '.reply-comment', function () {
    $('#parent_id').val($(this).data('id'));
    $('#news_id').val($(this).data('news-id'));
    $('#commentModalLabel').text('Reply to ' + ($(this).data('author') || 'comment'));
    $('#comment_text').val('');
    $('#commentModal').modal('show');
    setTimeout(() => $('#comment_text').focus(), 320);
  });

  /* ── Submit reply ── */
  $('#commentForm').on('submit', function (e) {
    e.preventDefault();
    const $btn = $(this).find('[type=submit]');
    $btn.prop('disabled', true).text('Posting…');
    $.post("{{ route('admin.comment.store') }}", $(this).serialize())
      .done(resp => {
        $('#commentModal').modal('hide');
        table.ajax.reload(null, false);
        toastr.success(resp.message || 'Reply posted.');
        this.reset();
      })
      .fail(() => toastr.error('Something went wrong.'))
      .always(() => $btn.prop('disabled', false).text('Post Reply'));
  });

  /* ── Open edit modal ── */
  $(document).on('click', '.edit-comment, .edit-reply', function () {
    $.get('/admin/comment/' + $(this).data('id') + '/edit')
      .done(resp => {
        $('#edit_comment_id').val(resp.id);
        $('#edit_comment_text').val(resp.comment);
        $('#editCommentModal').modal('show');
        setTimeout(() => $('#edit_comment_text').focus(), 320);
      })
      .fail(() => toastr.error('Could not load comment.'));
  });

  /* ── Submit edit ── */
  $('#editCommentForm').on('submit', function (e) {
    e.preventDefault();
    const id   = $('#edit_comment_id').val();
    const $btn = $(this).find('[type=submit]');
    $btn.prop('disabled', true).text('Saving…');
    $.ajax({ url: '/admin/comment/' + id, type: 'PUT', data: $(this).serialize() })
      .done(resp => {
        $('#editCommentModal').modal('hide');
        table.ajax.reload(null, false);
        toastr.success(resp.message || 'Comment updated.');
      })
      .fail(() => toastr.error('Something went wrong.'))
      .always(() => $btn.prop('disabled', false).text('Save Changes'));
  });

  /* ── Delete ── */
  $(document).on('click', '.delete-comment, .delete-reply', function () {
    const id      = $(this).data('id');
    const isReply = $(this).hasClass('delete-reply');
    Swal.fire({
      title: isReply ? 'Delete reply?' : 'Delete comment?',
      text:  'This cannot be undone.',
      icon:  'warning',
      showCancelButton:    true,
      confirmButtonText:   'Delete',
      cancelButtonText:    'Cancel',
      confirmButtonColor:  '#b91c1c'
    }).then(result => {
      if (result.isConfirmed) {
        $.ajax({ url: '/admin/comment/' + id, type: 'DELETE' })
          .done(resp => { table.ajax.reload(null, false); toastr.success(resp.message || 'Deleted.'); })
          .fail(() => toastr.error('Delete failed.'));
      }
    });
  });

  /* ── Filter pills (UI only — wire to server as needed) ── */
  $(document).on('click', '.filter-pill', function () {
    $('.filter-pill').removeClass('active');
    $(this).addClass('active');
    // table.ajax.url(...).load(); // extend with server filter if needed
  });

});
</script>
@endpush
