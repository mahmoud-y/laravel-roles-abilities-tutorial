@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Roles') }}</div>

                <div class="card-body">
                    <div class="form-group">
                        <label>{{ __('Name') }}</label>
                        <input type="text" class="form-control-plaintext" value="{{ $role->name }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('Abilities') }}</label>
                        <select multiple class="form-control" disabled>
                            @foreach($abilities as $ability)
                            <option value="{{ $ability->id }}" {{ !in_array($ability->id, old('abilities', $role->abilities()->pluck('abilities.id')->toArray())) ?: 'selected' }}>{{ $ability->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection