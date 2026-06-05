@extends('layouts.admin')

@section('title')
    {{ $node->name }}
@endsection

@section('content-header')
    <h1>{{ $node->name }}<small>A quick overview of your node.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.nodes') }}">Nodes</a></li>
        <li class="active">{{ $node->name }}</li>
    </ol>
@endsection

@section('content')
<style>
/* ─── KDE Plasma System Monitor Inspired ─── */
.alx-tabs {
    display: flex;
    gap: 4px;
    margin-bottom: 20px;
    background: rgba(15,23,42,0.6);
    border: 1px solid rgba(99,102,241,0.2);
    border-radius: 10px;
    padding: 6px;
}
.alx-tab {
    padding: 8px 18px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    text-decoration: none;
    transition: all 0.2s;
    border: 1px solid transparent;
}
.alx-tab:hover { color: #a5b4fc; background: rgba(99,102,241,0.1); text-decoration: none; }
.alx-tab.active {
    background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(139,92,246,0.15));
    border-color: rgba(99,102,241,0.4);
    color: #a5b4fc;
}

/* Info card */
.alx-card {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border: 1px solid rgba(99,102,241,0.2);
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.4);
    overflow: hidden;
    margin-bottom: 20px;
}
.alx-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 22px;
    border-bottom: 1px solid rgba(99,102,241,0.15);
    background: rgba(99,102,241,0.05);
}
.alx-card-title { font-size:14px; font-weight:600; color:#e2e8f0; display:flex; align-items:center; gap:8px; margin:0; }
.alx-card-title i { color:#818cf8; }

.alx-info-table { width:100%; border-collapse:collapse; }
.alx-info-table tr { border-bottom:1px solid rgba(255,255,255,0.04); }
.alx-info-table tr:last-child { border-bottom:none; }
.alx-info-table td { padding:14px 22px; font-size:13px; vertical-align:middle; }
.alx-info-table td:first-child { color:#64748b; font-weight:500; width:40%; }
.alx-info-table td:last-child { color:#e2e8f0; }
.alx-info-table td code {
    background: rgba(15,23,42,0.8); border:1px solid rgba(99,102,241,0.2);
    border-radius:4px; padding:2px 7px; font-size:11px; color:#7dd3fc;
}

/* ─── KDE Plasma Task Manager Resource Cards ─── */
.alx-resource-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    padding: 20px;
}
.alx-resource-card {
    background: rgba(15,23,42,0.6);
    border: 1px solid rgba(99,102,241,0.15);
    border-radius: 10px;
    padding: 18px;
    position: relative;
    overflow: hidden;
    transition: border-color 0.2s;
}
.alx-resource-card:hover { border-color: rgba(99,102,241,0.4); }
.alx-resource-card::before {
    content: '';
    position: absolute; top:0; left:0; right:0; height:2px;
    border-radius: 10px 10px 0 0;
}
.alx-res-disk::before { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
.alx-res-memory::before { background: linear-gradient(90deg, #06b6d4, #3b82f6); }
.alx-res-servers::before { background: linear-gradient(90deg, #10b981, #34d399); }
.alx-res-maint::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

.alx-resource-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; margin-bottom: 12px;
}
.alx-res-disk .alx-resource-icon { background: rgba(99,102,241,0.15); color: #818cf8; }
.alx-res-memory .alx-resource-icon { background: rgba(6,182,212,0.15); color: #22d3ee; }
.alx-res-servers .alx-resource-icon { background: rgba(16,185,129,0.15); color: #34d399; }
.alx-res-maint .alx-resource-icon { background: rgba(245,158,11,0.15); color: #fbbf24; }

.alx-resource-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: #475569; margin-bottom: 6px; }
.alx-resource-value { font-size: 20px; font-weight: 700; color: #e2e8f0; margin-bottom: 4px; line-height: 1; }
.alx-resource-sub { font-size: 11px; color: #64748b; margin-bottom: 14px; }

/* KDE-style progress bar */
.alx-progress-wrap {
    height: 6px;
    background: rgba(255,255,255,0.06);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 4px;
}
.alx-progress-bar {
    height: 100%;
    border-radius: 3px;
    transition: width 0.6s ease;
    position: relative;
}
.alx-res-disk .alx-progress-bar { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
.alx-res-memory .alx-progress-bar { background: linear-gradient(90deg, #06b6d4, #3b82f6); }
.alx-progress-bar::after {
    content: '';
    position: absolute; top:0; left:0; right:0; bottom:0;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.15) 50%, transparent 100%);
    background-size: 200% 100%;
    animation: shimmer 2s infinite;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
.alx-progress-label { font-size: 10px; color: #64748b; display: flex; justify-content: space-between; }

/* danger zone */
.alx-danger-card {
    background: rgba(239,68,68,0.04);
    border: 1px solid rgba(239,68,68,0.2);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 20px;
}
.alx-danger-header {
    padding: 14px 22px;
    border-bottom: 1px solid rgba(239,68,68,0.15);
    background: rgba(239,68,68,0.06);
    font-size: 13px; font-weight: 600; color: #f87171;
    display: flex; align-items: center; gap: 8px;
}
.alx-danger-body { padding: 16px 22px; font-size: 13px; color: #94a3b8; }
.alx-danger-footer { padding: 12px 22px; border-top: 1px solid rgba(239,68,68,0.1); display: flex; justify-content: flex-end; }
.alx-btn-danger {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 500;
    background: rgba(239,68,68,0.15); color: #f87171;
    border: 1px solid rgba(239,68,68,0.3); cursor: pointer;
    transition: all 0.2s; text-decoration: none;
}
.alx-btn-danger:hover { background: rgba(239,68,68,0.25); color: #fca5a5; }
.alx-btn-danger:disabled, .alx-btn-danger[disabled] { opacity: 0.4; cursor: not-allowed; pointer-events: none; }

/* description */
.alx-desc-card {
    background: rgba(15,23,42,0.4);
    border: 1px solid rgba(99,102,241,0.1);
    border-radius: 12px; padding: 18px 22px;
    margin-bottom: 20px;
}
.alx-desc-card pre { color: #94a3b8; font-size: 13px; margin: 0; white-space: pre-wrap; }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="alx-tabs">
            <a href="{{ route('admin.nodes.view', $node->id) }}" class="alx-tab active">About</a>
            <a href="{{ route('admin.nodes.view.settings', $node->id) }}" class="alx-tab">Settings</a>
            <a href="{{ route('admin.nodes.view.configuration', $node->id) }}" class="alx-tab">Configuration</a>
            <a href="{{ route('admin.nodes.view.allocation', $node->id) }}" class="alx-tab">Allocation</a>
            <a href="{{ route('admin.nodes.view.servers', $node->id) }}" class="alx-tab">Servers</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="alx-card">
            <div class="alx-card-header">
                <h3 class="alx-card-title"><i class="fa fa-pie-chart"></i> Real-Time Resource Allocation</h3>
            </div>
            <div class="alx-card-body" style="padding: 30px 15px;">
                <div style="display: flex; flex-wrap: nowrap; gap: 20px; overflow-x: auto; padding-bottom: 20px; -webkit-overflow-scrolling: touch;">
                    {{-- CPU Chart --}}
                    <div style="flex: 0 0 auto; width: 30%; min-width: 250px; text-align: center; margin: 0 auto;">
                        <h4 style="color: #e2e8f0; font-weight: 600; margin-bottom: 20px;">CPU Usage (%)</h4>
                        <div style="position: relative; width: 200px; height: 200px; margin: 0 auto;">
                            <canvas id="chartCpu"></canvas>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                <div id="cpuActiveText" style="font-size: 24px; font-weight: 700; color: #fff;">--%</div>
                                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Active</div>
                            </div>
                        </div>
                        <p id="cpuSubText" style="margin-top: 15px; font-size: 13px; color: #94a3b8;">-- of -- Cores Allocated</p>
                    </div>

                    {{-- Memory Chart --}}
                    <div style="flex: 0 0 auto; width: 30%; min-width: 250px; text-align: center; margin: 0 auto;">
                        <h4 style="color: #e2e8f0; font-weight: 600; margin-bottom: 20px;">Memory Usage (GiB)</h4>
                        <div style="position: relative; width: 200px; height: 200px; margin: 0 auto;">
                            <canvas id="chartMem"></canvas>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                <div id="memActiveText" style="font-size: 20px; font-weight: 700; color: #fff;">-- GiB</div>
                                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Active</div>
                            </div>
                        </div>
                        <p id="memSubText" style="margin-top: 15px; font-size: 13px; color: #94a3b8;">-- allocated of {{ number_format($node->memory / 1024, 1) }} GiB Total</p>
                    </div>

                    {{-- Disk Chart --}}
                    <div style="flex: 0 0 auto; width: 30%; min-width: 250px; text-align: center; margin: 0 auto;">
                        <h4 style="color: #e2e8f0; font-weight: 600; margin-bottom: 20px;">Disk Space Usage (GiB)</h4>
                        <div style="position: relative; width: 200px; height: 200px; margin: 0 auto;">
                            <canvas id="chartDisk"></canvas>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                <div id="diskActiveText" style="font-size: 20px; font-weight: 700; color: #fff;">-- GiB</div>
                                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Active</div>
                            </div>
                        </div>
                        <p id="diskSubText" style="margin-top: 15px; font-size: 13px; color: #94a3b8;">-- allocated of {{ number_format($node->disk / 1024, 1) }} GiB Total</p>
                    </div>
                </div>

                {{-- Status Widget --}}
                <div style="margin-top: 40px; padding: 20px; background: rgba(15,23,42,0.6); border: 1px solid rgba(99,102,241,0.2); border-radius: 12px; text-align: center;">
                    <h4 style="margin: 0; color: #94a3b8; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Remaining Physical Disk Space</h4>
                    <div id="diskRemaining" style="font-size: 28px; font-weight: 700; color: #4ade80; margin-top: 8px;">-- GiB</div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 5px;">(Total Node Capacity minus Real-Time Active Usage)</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- LEFT COL ─ System Info --}}
    <div class="col-sm-8">
        {{-- System Information --}}
        <div class="alx-card">
            <div class="alx-card-header">
                <h3 class="alx-card-title"><i class="fa fa-microchip"></i> System Information</h3>
                <span id="node-online-badge" style="display:none; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:600; background:rgba(34,197,94,0.15); color:#4ade80; border:1px solid rgba(34,197,94,0.3)">
                    <i class="fa fa-circle" style="font-size:8px"></i> Online
                </span>
            </div>
            <table class="alx-info-table">
                <tr>
                    <td><i class="fa fa-code-fork" style="margin-right:6px;color:#818cf8"></i> Daemon Version</td>
                    <td>
                        <code data-attr="info-version"><i class="fa fa-refresh fa-spin fa-fw"></i></code>
                        <span style="color:#475569; font-size:12px"> — Latest: <code>{{ $version->getDaemon() }}</code></span>
                    </td>
                </tr>
                <tr>
                    <td><i class="fa fa-linux" style="margin-right:6px;color:#818cf8"></i> OS</td>
                    <td data-attr="info-system"><i class="fa fa-refresh fa-spin fa-fw" style="color:#64748b"></i></td>
                </tr>
                <tr>
                    <td><i class="fa fa-tasks" style="margin-right:6px;color:#818cf8"></i> CPU Threads</td>
                    <td data-attr="info-cpus"><i class="fa fa-refresh fa-spin fa-fw" style="color:#64748b"></i></td>
                </tr>
                <tr>
                    <td><i class="fa fa-globe" style="margin-right:6px;color:#818cf8"></i> Address</td>
                    <td><code>{{ $node->fqdn }}:{{ $node->daemonListen }}</code></td>
                </tr>
            </table>
        </div>

        {{-- Description --}}
        @if ($node->description)
            <div class="alx-desc-card">
                <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.8px; color:#64748b; margin-bottom:10px">
                    <i class="fa fa-align-left" style="margin-right:5px"></i>Description
                </div>
                <pre>{{ $node->description }}</pre>
            </div>
        @endif
    </div>

    {{-- RIGHT COL ─ Servers & Delete --}}
    <div class="col-sm-4">
        {{-- Servers & Status --}}
        <div class="alx-card" style="margin-bottom: 20px;">
            <div class="alx-card-header">
                <h3 class="alx-card-title"><i class="fa fa-server"></i> Node Overview</h3>
            </div>
            <div class="alx-card-body" style="padding: 20px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                    <div><i class="fa fa-server" style="color: #6366f1; margin-right: 8px;"></i> <strong>Total Servers</strong></div>
                    <div style="font-size: 16px; font-weight: 600; color: #e2e8f0;">{{ $node->servers_count }}</div>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div><i class="fa fa-{{ $node->maintenance_mode ? 'wrench' : 'check-circle' }}" style="color: {{ $node->maintenance_mode ? '#fbbf24' : '#34d399' }}; margin-right: 8px;"></i> <strong>Status</strong></div>
                    <div style="font-size: 14px; font-weight: 600; color: {{ $node->maintenance_mode ? '#fbbf24' : '#34d399' }};">
                        {{ $node->maintenance_mode ? 'Maintenance' : 'Operational' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete --}}
        <div class="alx-danger-card">
            <div class="alx-danger-header"><i class="fa fa-trash"></i> Danger Zone — Delete Node</div>
            <div class="alx-danger-body">
                Deleting a node is <strong>irreversible</strong> and will immediately remove this node from the panel.
                There must be <strong>no servers</strong> associated with this node before proceeding.
            </div>
            <div class="alx-danger-footer">
                <form action="{{ route('admin.nodes.view.delete', $node->id) }}" method="POST">
                    {!! csrf_field() !!}
                    {!! method_field('DELETE') !!}
                    <button type="submit" class="alx-btn-danger" {{ ($node->servers_count < 1) ?: 'disabled' }}>
                        <i class="fa fa-trash"></i> Delete This Node
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="alx-card">
            <div class="alx-card-header">
                <h3 class="alx-card-title"><i class="fa fa-pie-chart"></i> Real-Time Resource Allocation</h3>
            </div>
            <div class="alx-card-body" style="padding: 30px 15px;">
                <div style="display: flex; flex-wrap: nowrap; gap: 20px; overflow-x: auto; padding-bottom: 20px; -webkit-overflow-scrolling: touch;">
                    {{-- CPU Chart --}}
                    <div style="flex: 0 0 auto; width: 30%; min-width: 250px; text-align: center; margin: 0 auto;">
                        <h4 style="color: #e2e8f0; font-weight: 600; margin-bottom: 20px;">CPU Usage (%)</h4>
                        <div style="position: relative; width: 200px; height: 200px; margin: 0 auto;">
                            <canvas id="chartCpu"></canvas>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                <div id="cpuActiveText" style="font-size: 24px; font-weight: 700; color: #fff;">--%</div>
                                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Active</div>
                            </div>
                        </div>
                        <p id="cpuSubText" style="margin-top: 15px; font-size: 13px; color: #94a3b8;">-- of -- Cores Allocated</p>
                    </div>

                    {{-- Memory Chart --}}
                    <div style="flex: 0 0 auto; width: 30%; min-width: 250px; text-align: center; margin: 0 auto;">
                        <h4 style="color: #e2e8f0; font-weight: 600; margin-bottom: 20px;">Memory Usage (GiB)</h4>
                        <div style="position: relative; width: 200px; height: 200px; margin: 0 auto;">
                            <canvas id="chartMem"></canvas>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                <div id="memActiveText" style="font-size: 20px; font-weight: 700; color: #fff;">-- GiB</div>
                                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Active</div>
                            </div>
                        </div>
                        <p id="memSubText" style="margin-top: 15px; font-size: 13px; color: #94a3b8;">-- allocated of {{ number_format($node->memory / 1024, 1) }} GiB Total</p>
                    </div>

                    {{-- Disk Chart --}}
                    <div style="flex: 0 0 auto; width: 30%; min-width: 250px; text-align: center; margin: 0 auto;">
                        <h4 style="color: #e2e8f0; font-weight: 600; margin-bottom: 20px;">Disk Space Usage (GiB)</h4>
                        <div style="position: relative; width: 200px; height: 200px; margin: 0 auto;">
                            <canvas id="chartDisk"></canvas>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                <div id="diskActiveText" style="font-size: 20px; font-weight: 700; color: #fff;">-- GiB</div>
                                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Active</div>
                            </div>
                        </div>
                        <p id="diskSubText" style="margin-top: 15px; font-size: 13px; color: #94a3b8;">-- allocated of {{ number_format($node->disk / 1024, 1) }} GiB Total</p>
                    </div>
                </div>

                {{-- Status Widget --}}
                <div style="margin-top: 40px; padding: 20px; background: rgba(15,23,42,0.6); border: 1px solid rgba(99,102,241,0.2); border-radius: 12px; text-align: center;">
                    <h4 style="margin: 0; color: #94a3b8; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Remaining Physical Disk Space</h4>
                    <div id="diskRemaining" style="font-size: 28px; font-weight: 700; color: #4ade80; margin-top: 8px;">-- GiB</div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 5px;">(Total Node Capacity minus Real-Time Active Usage)</div>
                </div>
            </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    (function getInformation() {
        $.ajax({
            method: 'GET',
            url: '/admin/nodes/view/{{ $node->id }}/system-information',
            timeout: 5000,
        }).done(function (data) {
            $('[data-attr="info-version"]').html(escapeHtml(data.version));
            $('[data-attr="info-system"]').html(
                '<span style="color:#e2e8f0">' + escapeHtml(data.system.type) + '</span>' +
                ' <span style="color:#64748b">(' + escapeHtml(data.system.arch) + ')</span>' +
                ' <code>' + escapeHtml(data.system.release) + '</code>'
            );
            $('[data-attr="info-cpus"]').html(
                '<span style="color:#e2e8f0; font-weight:600">' + data.system.cpus + '</span>' +
                ' <span style="color:#64748b; font-size:12px">logical cores</span>'
            );
            $('#node-online-badge').fadeIn(200);
            
            // Update the CPU allocated cores text since we now know the physical cores
            $('#cpuSubText').text('of ' + data.system.cpus + ' Physical Cores');
            window.nodePhysicalCores = data.system.cpus;
        }).fail(function () {
            $('[data-attr="info-version"]').html('<span style="color:#f87171">Unreachable</span>');
        }).always(function() {
            setTimeout(getInformation, 10000);
        });
    })();

    // Chart.js Setup
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = 'Inter, sans-serif';

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(15,23,42,0.9)',
                titleColor: '#fff',
                bodyColor: '#cbd5e1',
                borderColor: 'rgba(99,102,241,0.3)',
                borderWidth: 1,
                callbacks: {
                    label: function(context) {
                        return ' ' + context.label + ': ' + context.formattedValue + (context.chart.canvas.id === 'chartCpu' ? '%' : ' GiB');
                    }
                }
            }
        }
    };

    // Initialize Charts with empty datasets
    const chartCpu = new Chart(document.getElementById('chartCpu'), {
        type: 'doughnut',
        data: {
            labels: ['Active Usage', 'Free'],
            datasets: [{
                data: [0, 100],
                backgroundColor: ['#6366f1', 'rgba(30,41,59,0.5)'],
                borderWidth: 0,
                borderRadius: 5
            }]
        },
        options: commonOptions
    });

    const chartMem = new Chart(document.getElementById('chartMem'), {
        type: 'doughnut',
        data: {
            labels: ['Active Usage', 'Allocated', 'Free'],
            datasets: [
                {
                    // Inner ring: Active Usage vs Total
                    data: [0, 1],
                    backgroundColor: ['#10b981', 'rgba(30,41,59,0.0)'], // inner uses green
                    borderWidth: 0,
                    weight: 1
                },
                {
                    // Outer ring: Allocated vs Total
                    data: [0, 1],
                    backgroundColor: ['#3b82f6', 'rgba(30,41,59,0.5)'], // outer uses blue
                    borderWidth: 0,
                    weight: 0.5
                }
            ]
        },
        options: { ...commonOptions, cutout: '65%' }
    });

    const chartDisk = new Chart(document.getElementById('chartDisk'), {
        type: 'doughnut',
        data: {
            labels: ['Active Usage', 'Allocated', 'Free'],
            datasets: [
                {
                    data: [0, 1],
                    backgroundColor: ['#8b5cf6', 'rgba(30,41,59,0.0)'],
                    borderWidth: 0,
                    weight: 1
                },
                {
                    data: [0, 1],
                    backgroundColor: ['#f59e0b', 'rgba(30,41,59,0.5)'],
                    borderWidth: 0,
                    weight: 0.5
                }
            ]
        },
        options: { ...commonOptions, cutout: '65%' }
    });

    // Fetch Live Usage
    const nodeTotalMem = {{ $node->memory }} / 1024; // GiB
    const nodeTotalDisk = {{ $node->disk }} / 1024; // GiB
    @php
        $memAllocated = (float) str_replace(',', '', $stats['memory']['value']) / 1024;
        $diskAllocated = (float) str_replace(',', '', $stats['disk']['value']) / 1024;
    @endphp
    const memAlloc = {{ $memAllocated }};
    const diskAlloc = {{ $diskAllocated }};

    function fetchLiveUsage() {
        $.ajax({
            method: 'GET',
            url: '/admin/nodes/view/{{ $node->id }}/live-usage',
            timeout: 8000,
        }).done(function (data) {
            let activeCpu = data.active.cpu;
            let activeMem = data.active.memory_bytes / 1024 / 1024 / 1024;
            let activeDisk = data.active.disk_bytes / 1024 / 1024 / 1024;

            // Optional: calculate CPU % relative to physical cores if known, otherwise just show absolute total
            let cpuPercent = activeCpu;
            if (window.nodePhysicalCores) {
                cpuPercent = activeCpu / window.nodePhysicalCores;
            }

            // Update UI Texts
            $('#cpuActiveText').text(cpuPercent.toFixed(1) + '%');
            $('#memActiveText').text(activeMem.toFixed(1) + ' GiB');
            $('#diskActiveText').text(activeDisk.toFixed(1) + ' GiB');
            $('#memSubText').text(memAlloc.toFixed(1) + ' GiB Allocated of ' + nodeTotalMem.toFixed(1) + ' GiB Total');
            $('#diskSubText').text(diskAlloc.toFixed(1) + ' GiB Allocated of ' + nodeTotalDisk.toFixed(1) + ' GiB Total');

            // Remaining Physical Disk = Node Total Disk - Active Used Disk
            let remainingDisk = Math.max(0, nodeTotalDisk - activeDisk);
            $('#diskRemaining').text(remainingDisk.toFixed(1) + ' GiB');

            // Update CPU Chart
            chartCpu.data.datasets[0].data = [cpuPercent, Math.max(0, 100 - cpuPercent)];
            chartCpu.update();

            // Update Memory Chart (Inner: Active, Outer: Allocated)
            chartMem.data.datasets[0].data = [activeMem, Math.max(0, nodeTotalMem - activeMem)];
            chartMem.data.datasets[1].data = [memAlloc, Math.max(0, nodeTotalMem - memAlloc)];
            chartMem.update();

            // Update Disk Chart
            chartDisk.data.datasets[0].data = [activeDisk, Math.max(0, nodeTotalDisk - activeDisk)];
            chartDisk.data.datasets[1].data = [diskAlloc, Math.max(0, nodeTotalDisk - diskAlloc)];
            chartDisk.update();

        }).always(function() {
            setTimeout(fetchLiveUsage, 10000);
        });
    }
    
    // Start polling
    fetchLiveUsage();

    </script>
@endsection
