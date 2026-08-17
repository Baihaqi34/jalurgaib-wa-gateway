@extends('layouts.app')

@section('title', 'Dashboard — JalurGaib WA Gateway')

@section('content')
<script>
    window.location.href = "{{ route('user.dashboard') }}";
</script>
<div class="mono-card p-8 text-center text-mono-400">
    <p>Mengalihkan ke Portal Dashboard...</p>
</div>
@endsection
