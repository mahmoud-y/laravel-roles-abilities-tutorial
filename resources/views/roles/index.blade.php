@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <span>{{ __('Roles') }}</span>
                        @can('create-role')
                        <a href="{{ route('roles.create') }}" class="btn btn-primary">{{ __('Create') }}</a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    @if($roles->isEmpty())
                    {{ __('There are no roles!') }}
                    @else
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <th>{{ __('Name') }}</th>
                            @canany(['view-role', 'update-role', 'delete-role'])
                            <th>{{ __('Action') }}</th>
                            @endcanany
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                @canany(['view-role', 'update-role', 'delete-role'])
                                <td>
                                    @can('view-role')
                                    <a href="{{ route('roles.show', ['role' => $role]) }}" class="btn btn-primary">{{ __('View') }}</a>
                                    @endcan
                                    @can('update-role')
                                    <a href="{{ route('roles.edit', ['role' => $role]) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('delete-role')
                                    <form action="{{ route('roles.destroy', ['role' => $role]) }}" method="post" class="d-inline">
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