@props(['type' => 'info', 'message'])

@php
    $classes = match($type) {
        'success' => 'bg-teal-500 border-teal-600',
        'error' => 'bg-red-500 border-red-600',
        'warning' => 'bg-yellow-500 border-yellow-600',
        'info' => 'bg-blue-500 border-blue-600',
        default => 'bg-gray-500 border-gray-600',
    };
@endphp

<div class="alert alert-{{ $type }} p-4 mb-4 rounded-lg border text-white" role="alert">
    {{ $message }}
</div>
