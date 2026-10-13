@extends('layouts.admin')

@section('title', 'Kazananı Düzenle')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Kazananı Düzenle</h1>
            <p class="text-muted mb-0">{{ $winner->company_name }}</p>
        </div>

        <a href="{{ route('admin.winners.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Listeye Dön
        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form method="POST" action="{{ route('admin.winners.update', $winner) }}">

                @csrf
                @method('PUT')

                @include('admin.winners._fields')

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>
                        Kaydet
                    </button>

                    <a href="{{ route('admin.winners.index') }}" class="btn btn-light">
                        İptal
                    </a>
                </div>

            </form>

        </div>

    </div>

@endsection
