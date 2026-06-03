@extends('layouts.admin')

@section('title')
    Announcements
@endsection

@section('content-header')
    <h1>Announcements<small>Create and manage panel-wide announcements.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Announcements</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Announcement List</h3>
                <div class="box-tools">
                    <a href="{{ route('admin.announcements.new') }}" class="btn btn-sm btn-primary">Create New</a>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th></th>
                        </tr>
                        @foreach ($announcements as $announcement)
                            <tr>
                                <td>{{ $announcement->id }}</td>
                                <td><strong>{{ $announcement->title }}</strong></td>
                                <td>
                                    <span class="label label-{{ $announcement->type === 'critical' ? 'danger' : ($announcement->type === 'warning' ? 'warning' : 'info') }}">
                                        {{ strtoupper($announcement->type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($announcement->is_active)
                                        <span class="label label-success">Active</span>
                                    @else
                                        <span class="label label-default">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $announcement->author->username }}</td>
                                <td>{{ $announcement->created_at->diffForHumans() }}</td>
                                <td class="text-right">
                                    <form action="{{ route('admin.announcements.delete', $announcement->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this announcement?')"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($announcements->hasPages())
                <div class="box-footer with-border">
                    <div class="col-md-12 text-center">{!! $announcements->appends(['query' => request()->input('query')])->render() !!}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
