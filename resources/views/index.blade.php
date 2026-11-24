<style>
    .activity-stat-card {
        border-radius: 8px;
        transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .activity-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .activity-stat-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 22px;
    }
    .diff-table td {
        vertical-align: top;
        font-family: Consolas, "Courier New", monospace;
        font-size: 13px;
    }
    .diff-old {
        background-color: #ffeef0;
        color: #b31d28;
    }
    .diff-new {
        background-color: #e6ffed;
        color: #22863a;
    }
</style>

@if(!$stats['installed'])
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="icon-exclamation-triangle me-2 fs-5"></i>
    <div>
        {{ trans('lavero-admin-activity-log::activity.not_installed') }}
    </div>
</div>
@endif

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card activity-stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="activity-stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="icon-history"></i>
                </div>
                <div>
                    <h6 class="card-subtitle text-muted mb-1">{{ trans('lavero-admin-activity-log::activity.total_activities') }}</h6>
                    <h3 class="card-title mb-0 fw-bold">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card activity-stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="activity-stat-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="icon-calendar-check"></i>
                </div>
                <div>
                    <h6 class="card-subtitle text-muted mb-1">{{ trans('lavero-admin-activity-log::activity.today_activities') }}</h6>
                    <h3 class="card-title mb-0 fw-bold">{{ $stats['today'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card activity-stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="activity-stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="icon-pencil-alt"></i>
                </div>
                <div>
                    <h6 class="card-subtitle text-muted mb-1">{{ trans('lavero-admin-activity-log::activity.updated_events') }}</h6>
                    <h3 class="card-title mb-0 fw-bold">{{ $stats['updated'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card activity-stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="activity-stat-icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="icon-trash"></i>
                </div>
                <div>
                    <h6 class="card-subtitle text-muted mb-1">{{ trans('lavero-admin-activity-log::activity.deleted_events') }}</h6>
                    <h3 class="card-title mb-0 fw-bold">{{ $stats['deleted'] }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
        <div class="d-flex align-items-center gap-2">
            <h5 class="card-title mb-0">
                <i class="icon-history me-1 text-primary"></i> {{ trans('lavero-admin-activity-log::activity.title') }}
            </h5>
            <span class="badge bg-light text-dark border">{{ count($activities) }}</span>
        </div>

        <form method="get" action="{{ route('activity-log-index') }}" class="d-flex align-items-center gap-2 flex-wrap">
            <select name="event" class="form-select form-select-sm" style="width: auto;">
                <option value="">{{ trans('lavero-admin-activity-log::activity.all_events') }}</option>
                <option value="created" @if(($filters['event'] ?? '') === 'created') selected @endif>{{ trans('lavero-admin-activity-log::activity.event_created') }}</option>
                <option value="updated" @if(($filters['event'] ?? '') === 'updated') selected @endif>{{ trans('lavero-admin-activity-log::activity.event_updated') }}</option>
                <option value="deleted" @if(($filters['event'] ?? '') === 'deleted') selected @endif>{{ trans('lavero-admin-activity-log::activity.event_deleted') }}</option>
            </select>

            <input type="text" name="subject_type" class="form-control form-control-sm" placeholder="{{ trans('lavero-admin-activity-log::activity.subject') }}" value="{{ $filters['subject_type'] ?? '' }}" style="width: 160px;">

            <button type="submit" class="btn btn-sm btn-primary">
                <i class="icon-search me-1"></i> {{ trans('lavero-admin-activity-log::activity.filter') }}
            </button>
            <a href="{{ route('activity-log-index') }}" class="btn btn-sm btn-outline-secondary" title="{{ trans('admin.reset') }}">
                <i class="icon-undo"></i>
            </a>

            <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="btn-clean-activities">
                <i class="icon-trash me-1"></i> {{ trans('lavero-admin-activity-log::activity.clean_old') }}
            </button>
        </form>
    </div>

    @if(empty($activities))
        <div class="card-body text-center py-5 text-muted">
            <i class="icon-file-alt fs-1 mb-2 opacity-50"></i>
            <h6 class="fw-normal">{{ trans('lavero-admin-activity-log::activity.empty') }}</h6>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 100px;">{{ trans('lavero-admin-activity-log::activity.event') }}</th>
                        <th>{{ trans('lavero-admin-activity-log::activity.subject') }}</th>
                        <th>{{ trans('lavero-admin-activity-log::activity.description_col') }}</th>
                        <th style="width: 140px;">{{ trans('lavero-admin-activity-log::activity.causer') }}</th>
                        <th style="width: 180px;">{{ trans('lavero-admin-activity-log::activity.created_at') }}</th>
                        <th style="width: 120px;" class="text-end pe-3">{{ trans('lavero-admin-activity-log::activity.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $act)
                    <tr>
                        <td class="text-muted">{{ $act['id'] }}</td>
                        <td>
                            @if($act['event'] === 'created')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">{{ $act['event'] }}</span>
                            @elseif($act['event'] === 'updated')
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $act['event'] }}</span>
                            @elseif($act['event'] === 'deleted')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">{{ $act['event'] }}</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $act['event'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $act['subject_short'] }}</div>
                            <small class="text-muted">ID: {{ $act['subject_id'] }}</small>
                        </td>
                        <td>
                            <div class="text-break">{{ $act['description'] }}</div>
                        </td>
                        <td>
                            @if($act['causer_id'])
                                <div><span class="badge bg-light text-dark border">{{ $act['causer_short'] }} #{{ $act['causer_id'] }}</span></div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="small">{{ $act['created_at'] }}</div>
                            <small class="text-muted">{{ $act['age'] }}</small>
                        </td>
                        <td class="text-end pe-3">
                            @if($act['has_diff'])
                                <button type="button" class="btn btn-sm btn-outline-info btn-activity-detail" data-id="{{ $act['id'] }}">
                                    <i class="icon-eye me-1"></i> Diff
                                </button>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Modal: Diff Viewer -->
<div class="modal fade" id="modal-activity-diff" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-activity-title">{{ trans('lavero-admin-activity-log::activity.diff_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('admin.close') }}"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered diff-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">{{ trans('lavero-admin-activity-log::activity.field') }}</th>
                                <th style="width: 37.5%;">{{ trans('lavero-admin-activity-log::activity.old_value') }}</th>
                                <th style="width: 37.5%;">{{ trans('lavero-admin-activity-log::activity.new_value') }}</th>
                            </tr>
                        </thead>
                        <tbody id="modal-diff-body">
                        </tbody>
                    </table>
                </div>

                <div class="p-3 bg-light border-top">
                    <h6 class="fw-bold mb-2">{{ trans('lavero-admin-activity-log::activity.raw_properties') }}</h6>
                    <pre class="mb-0 bg-dark text-light p-3 rounded" style="font-size: 12px; max-height: 250px; overflow-y: auto;" id="modal-diff-raw"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ admin_trans('admin.close') }}</button>
            </div>
        </div>
    </div>
</div>

<script data-exec-on-popstate>
(function () {
    const detailUrl = '{{ route("activity-log-detail") }}';
    const cleanUrl = '{{ route("activity-log-clean") }}';

    document.querySelectorAll('.btn-activity-detail').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            admin.ajax.get(detailUrl + '?id=' + id, {}, function (res) {
                if (res && res.data && res.data.status && res.data.data) {
                    const data = res.data.data;
                    document.getElementById('modal-activity-title').textContent = (data.subject_type || 'Model') + ' (#' + data.subject_id + ') — ' + data.event;

                    const tbody = document.getElementById('modal-diff-body');
                    tbody.innerHTML = '';

                    if (data.diff && data.diff.length > 0) {
                        data.diff.forEach(item => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = '<td class="fw-bold">' + item.field + '</td>' +
                                           '<td class="diff-old">' + (item.old || '<span class="text-muted">null</span>') + '</td>' +
                                           '<td class="diff-new">' + (item.new || '<span class="text-muted">null</span>') + '</td>';
                            tbody.appendChild(tr);
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="3" class="text-center py-3 text-muted">{{ trans('lavero-admin-activity-log::activity.no_diff') }}</td></tr>';
                    }

                    document.getElementById('modal-diff-raw').textContent = data.raw;
                    const modal = new bootstrap.Modal(document.getElementById('modal-activity-diff'));
                    modal.show();
                }
            });
        });
    });

    const btnClean = document.getElementById('btn-clean-activities');
    if (btnClean) {
        btnClean.addEventListener('click', function () {
            Swal.fire({
                title: '{{ trans("lavero-admin-activity-log::activity.clean_confirm") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ trans("lavero-admin-activity-log::activity.clean_old") }}',
                cancelButtonText: '{{ admin_trans("admin.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    admin.ajax.post(cleanUrl, { days: 30 }, function (res) {
                        if (res && res.data && res.data.status) {
                            admin.toastr.success(res.data.message);
                            setTimeout(() => admin.ajax.reload(), 600);
                        } else {
                            admin.toastr.error(res && res.data ? res.data.message : 'Error');
                        }
                    });
                }
            });
        });
    }
})();
</script>
