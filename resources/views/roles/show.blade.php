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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection