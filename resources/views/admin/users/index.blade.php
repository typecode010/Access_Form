@extends('layouts.role-app')

@section('content')
    <section aria-labelledby="page-title">
        <h2 id="page-title" class="h3 mb-3">{{ $pageTitle }}</h2>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        <p class="text-muted mb-3">Manage platform users and assign one primary role per user.</p>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <caption class="visually-hidden">User role management table</caption>
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Current Role</th>
                        <th scope="col">Change Role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @php($currentRole = $user->roles->pluck('name')->first() ?? 'None')
                                <span class="badge {{ $currentRole === 'Admin' ? 'text-bg-primary' : ($currentRole === 'FormCreator' ? 'text-bg-success' : ($currentRole === 'Respondent' ? 'text-bg-dark' : 'text-bg-secondary')) }}">
                                    {{ $currentRole }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.role.update', $user) }}" class="d-flex flex-wrap gap-2 align-items-center">
                                    @csrf
                                    @method('PATCH')
                                    <label class="visually-hidden" for="role_user_{{ $user->id }}">Role for {{ $user->email }}</label>
                                    <select id="role_user_{{ $user->id }}" name="role" class="form-select form-select-sm" style="max-width: 180px;">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" @selected($currentRole === $role->name)>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </section>
@endsection
