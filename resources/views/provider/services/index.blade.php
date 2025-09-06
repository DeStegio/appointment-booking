<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
</head>
<body>
    <h1>Services</h1>

    @if (session('status'))
        <div role="status">{{ session('status') }}</div>
    @endif

    <p>
        <a href="{{ route('provider.services.create') }}">Create service</a>
    </p>

    <table border="1" cellpadding="6" cellspacing="0" aria-label="Services">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Duration (minutes)</th>
                <th scope="col">Price</th>
                <th scope="col">Active</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($services as $service)
            <tr>
                <td>{{ $service->name }}</td>
                <td>{{ $service->duration_minutes }}</td>
                <td>{{ number_format((float) $service->price, 2) }}</td>
                <td>{{ $service->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('provider.services.edit', $service) }}">Edit</a>

                    <form action="{{ route('provider.services.destroy', $service) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this service?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No services found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div>
        {{ $services->links() }}
    </div>
</body>
</html>

