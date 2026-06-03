@extends('layouts.admin')

@section('title')
    Create Announcement
@endsection

@section('content-header')
    <h1>Announcements<small>Create a new announcement.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.announcements') }}">Announcements</a></li>
        <li class="active">Create</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">New Announcement</h3>
            </div>
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <div class="box-body">
                    <div class="form-group">
                        <label for="title" class="control-label">Title <span class="field-required"></span></label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required />
                        <p class="text-muted small">Internal title or short heading for the announcement.</p>
                    </div>

                    <div class="form-group">
                        <label for="content" class="control-label">Content <span class="field-required"></span></label>
                        <textarea name="content" id="content" class="form-control" rows="4" required>{{ old('content') }}</textarea>
                        <p class="text-muted small">Markdown is supported. This will be displayed to the user.</p>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="type" class="control-label">Type <span class="field-required"></span></label>
                            <select name="type" id="type" class="form-control">
                                <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>Info</option>
                                <option value="promo" {{ old('type') == 'promo' ? 'selected' : '' }}>Promo</option>
                                <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>Warning</option>
                                <option value="critical" {{ old('type') == 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="priority" class="control-label">Priority <span class="field-required"></span></label>
                            <input type="number" name="priority" id="priority" class="form-control" value="{{ old('priority', 2) }}" min="1" max="4" required />
                            <p class="text-muted small">Higher number displays first. (1-4)</p>
                        </div>
                        
                        <div class="form-group col-md-4">
                            <label for="expires_at" class="control-label">Expires At</label>
                            <input type="datetime-local" name="expires_at" id="expires_at" class="form-control" value="{{ old('expires_at') }}" />
                            <p class="text-muted small">Leave blank for no expiration.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Target Display</label>
                        <div>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="target_display[]" value="dashboard" checked> Dashboard
                            </label>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="target_display[]" value="console" checked> Console
                            </label>
                        </div>
                        <p class="text-muted small">Where should this announcement be displayed?</p>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Status</label>
                        <div>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="is_active" value="1" checked> Active (Publish immediately)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-success pull-right">Create Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
