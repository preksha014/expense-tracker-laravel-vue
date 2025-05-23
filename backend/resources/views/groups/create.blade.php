@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Create Group</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('groups.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Group Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('groups.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Group</button>
            </div>
        </form>
    </div>
</div>
@endsection