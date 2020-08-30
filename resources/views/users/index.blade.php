@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('Users') }}</span>
                        @can('create-user')
                        <a href="{{ route('users.create') }}" class="btn btn-primary">{{ __('Create') }}</a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    @if($users->isEmpty())
                    {{ __('There are no users!') }}
                    @else
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            @canany(['view-user', 'update-user', 'delete-user'])
                            <th>{{ __('Action') }}</th>
                            @endcanany
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                @canany(['view-user', 'update-user', 'delete-user'])
                                <td>
                                    @can('view-user')
                                    <a href="{{ route('users.show', ['user' => $user]) }}" class="btn btn-primary">{{ __('View') }}</a>
                                    @endcan
                                    @can('update-user')
                                    <a href="{{ route('users.edit', ['user' => $user]) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('delete-user')
                                    <form action="{{ route('users.destroy', ['user' => $user]) }}" method="post" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="if (!confirm('delete?')) {event.preventDefault()}">{{ __('Delete') }}</button>
                                    </form>
                                    @endcan
                                </td>
                                @endcanany
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection