@extends('layouts.admin')

@section('title')
    Expiration Manager
@endsection

@section('content-header')
    <h1>Expiration Manager<small>Manage server expiration dates.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Expiration Manager</li>
    </ol>
@endsection

@section('content')
<style>
.alx-card {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border: 1px solid rgba(99,102,241,0.2);
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.4);
    overflow: hidden;
}
.alx-card-header {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    padding: 18px 24px; border-bottom: 1px solid rgba(99,102,241,0.15);
    background: rgba(99,102,241,0.05);
}
.alx-card-title { font-size:15px; font-weight:600; color:#e2e8f0; display:flex; align-items:center; gap:8px; margin:0; }
.alx-card-title i { color:#818cf8; }
.alx-search-group { display:flex; gap:8px; align-items:center; flex-wrap: wrap; }
.alx-search-group .form-control {
    background: rgba(15,23,42,0.8) !important; border: 1px solid rgba(99,102,241,0.3) !important;
    border-radius: 8px !important; color:#cbd5e1 !important; font-size:13px; height:34px;
    padding: 0 12px; min-width:180px; transition: border-color 0.2s;
}
.alx-search-group .form-control:focus { border-color:#818cf8 !important; box-shadow:0 0 0 3px rgba(129,140,248,0.1) !important; outline:none; }
.alx-search-group .form-control::placeholder { color:#64748b; }
.alx-btn { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:8px; font-size:12px; font-weight:500; border:none; cursor:pointer; transition:all 0.2s; text-decoration:none; white-space:nowrap; }
.alx-btn-search { background:rgba(99,102,241,0.15); color:#818cf8; border:1px solid rgba(99,102,241,0.3); }
.alx-btn-search:hover { background:rgba(99,102,241,0.25); color:#a5b4fc; }
.alx-table { width:100%; border-collapse:collapse; min-width: 700px; }
.alx-table thead tr { background:rgba(15,23,42,0.6); border-bottom:1px solid rgba(99,102,241,0.2); }
.alx-table thead th { padding:12px 16px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.8px; color:#64748b; white-space:nowrap; }
.alx-table tbody tr { border-bottom:1px solid rgba(255,255,255,0.04); transition:background 0.15s; }
.alx-table tbody tr:last-child { border-bottom:none; }
.alx-table tbody tr:hover { background:rgba(99,102,241,0.06); }
.alx-table td { padding:12px 16px; font-size:13px; color:#94a3b8; vertical-align:middle; }
.alx-table td a { color:#a5b4fc; text-decoration:none; font-weight:500; transition:color 0.15s; }
.alx-table td a:hover { color:#c7d2fe; }
.alx-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:600; white-space:nowrap; }
.alx-badge-unlimited { background:rgba(100,116,139,0.15); color:#94a3b8; border:1px solid rgba(100,116,139,0.25); }
.alx-badge-expired { background:rgba(239,68,68,0.15); color:#f87171; border:1px solid rgba(239,68,68,0.3); animation: pulse-red 2s infinite; }
.alx-badge-active { background:rgba(34,197,94,0.12); color:#4ade80; border:1px solid rgba(34,197,94,0.25); }
.alx-badge-soon { background:rgba(234,179,8,0.12); color:#facc15; border:1px solid rgba(234,179,8,0.25); }
@keyframes pulse-red { 0%,100%{opacity:1} 50%{opacity:0.6} }
.alx-action-group { display:flex; align-items:center; gap:6px; }
.alx-date-input {
    background: rgba(15,23,42,0.8) !important; border: 1px solid rgba(99,102,241,0.3) !important;
    border-radius:8px !important; color:#cbd5e1 !important; font-size:12px; height:32px;
    padding:0 10px; min-width:130px; transition:border-color 0.2s;
}
.alx-date-input:focus { border-color:#818cf8 !important; outline:none; }
.alx-btn-set { background:rgba(99,102,241,0.2); color:#818cf8; border:1px solid rgba(99,102,241,0.35); height:32px; padding:0 10px; border-radius:8px; font-size:12px; font-weight:500; cursor:pointer; transition:all 0.2s; white-space:nowrap; }
.alx-btn-set:hover { background:rgba(99,102,241,0.35); color:#a5b4fc; }
.alx-btn-add30 { background:rgba(34,197,94,0.12); color:#4ade80; border:1px solid rgba(34,197,94,0.25); height:32px; padding:0 10px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; transition:all 0.2s; white-space:nowrap; }
.alx-btn-add30:hover { background:rgba(34,197,94,0.25); }
.alx-pagination { display:flex; justify-content:center; padding:16px 24px; border-top:1px solid rgba(99,102,241,0.15); }
.alx-server-name { font-weight:600; color:#e2e8f0; }
.alx-username { color:#a5b4fc; font-size:13px; }
.alx-email { color:#64748b; font-size:11px; display:block; margin-top:2px; }
.alx-date-val { color:#e2e8f0; font-weight:500; font-size:13px; }
.alx-date-time { color:#64748b; font-size:11px; display:block; margin-top:1px; }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="alx-card">
            <div class="alx-card-header">
                <h3 class="alx-card-title"><i class="fa fa-clock-o"></i> Expiration Manager</h3>
                <form action="{{ route('admin.expiration') }}" method="GET">
                    <div class="alx-search-group">
                        <input type="text" name="filter[*]" class="form-control" value="{{ request()->input()['filter']['*'] ?? '' }}" placeholder="Search server name...">
                        <button type="submit" class="alx-btn alx-btn-search"><i class="fa fa-search"></i> Search</button>
                    </div>
                </form>
            </div>

            <div style="overflow-x: auto;">
                <table class="alx-table">
                    <thead>
                        <tr>
                            <th>Server</th>
                            <th>Owner</th>
                            <th>Node</th>
                            <th>Expires At</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servers as $server)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.servers.view', $server->id) }}" class="alx-server-name">
                                        <i class="fa fa-server" style="color:#6366f1;margin-right:6px;font-size:11px;"></i>{{ $server->name }}
                                    </a>
                                </td>

                                <td>
                                    <a href="{{ route('admin.users.view', $server->user->id) }}" class="alx-username">{{ $server->user->username }}</a>
                                    <small class="alx-email">{{ $server->user->email }}</small>
                                </td>

                                <td>
                                    <span style="color:#94a3b8;">{{ $server->node->name }}</span>
                                </td>

                                <td>
                                    @if($server->expires_at)
                                        <span class="alx-date-val">{{ $server->expires_at->format('d M Y') }}</span>
                                        <small class="alx-date-time"><i class="fa fa-clock-o" style="font-size:10px;"></i> {{ $server->expires_at->format('H:i') }}</small>
                                    @else
                                        <span style="color:#475569;">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if(!$server->expires_at)
                                        <span class="alx-badge alx-badge-unlimited"><i class="fa fa-infinity"></i> Unlimited</span>
                                    @elseif($server->expires_at->isPast())
                                        <span class="alx-badge alx-badge-expired"><i class="fa fa-exclamation-circle"></i> EXPIRED</span>
                                    @elseif($server->expires_at->diffInDays() <= 3)
                                        <span class="alx-badge alx-badge-soon"><i class="fa fa-clock-o"></i> {{ $server->expires_at->diffForHumans() }}</span>
                                    @else
                                        <span class="alx-badge alx-badge-active"><i class="fa fa-check-circle"></i> {{ $server->expires_at->diffForHumans() }}</span>
                                    @endif
                                </td>

                                <td>
                                    <form action="{{ route('admin.expiration.update', $server->id) }}" method="POST">
                                        {!! csrf_field() !!}
                                        <div class="alx-action-group">
                                            <input type="date" name="new_date" class="alx-date-input" title="Pick a specific date">
                                            <button type="submit" class="alx-btn-set" title="Set to selected date">
                                                <i class="fa fa-calendar-check-o"></i> Set
                                            </button>
                                            <button type="submit" name="days" value="30" class="alx-btn-add30" title="Add 30 Days from now/current expiry">
                                                +30D
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($servers->hasPages())
                <div class="alx-pagination">
                    <div class="col-md-12 text-center">{!! $servers->render() !!}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection