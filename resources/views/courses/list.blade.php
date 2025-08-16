@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/course_table.css') }}">

    <div class="container">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h1 class="fw-bold text-primary m-0">{{ __('messages.course_list') }}</h1>
            <a href="{{ route('profile') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        @php
            use \Illuminate\Support\Str;

            $blocks = [
                ['key' => 'bangkok', 'location' => $location_bangkok, 'courses' => $courses_bangkok],
                ['key' => 'saraburi', 'location' => $location_saraburi, 'courses' => $courses_saraburi],
                ['key' => 'hadyai',  'location' => $location_hadyai,  'courses' => $courses_hadyai],
                ['key' => 'phuket',  'location' => $location_phuket,  'courses' => $courses_phuket],
                ['key' => 'surin',   'location' => $location_surin,   'courses' => $courses_surin],
            ];

            // pick the first location that actually has courses as default (fallback to first card)
            $firstWithCourses = collect($blocks)->first(fn($b) => isset($b['courses']) && count($b['courses']) > 0) ?? $blocks[0];
            $queryDefault = request('loc'); // allow ?loc=bangkok etc.
            $defaultKey = $queryDefault ?: $firstWithCourses['key'];
        @endphp

        {{-- Location cards --}}
        <div class="row g-3 mb-4">
            @foreach($blocks as $b)
                @php
                    $loc = $b['location'] ?? null;
                    $courses = $b['courses'] ?? [];
                    if (!$loc) continue;

                    $panelId = 'panel-' . Str::slug($b['key']);
                    $isActive = $defaultKey === $b['key'];
                @endphp

                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card h-100 shadow-sm js-loc-card {{ $isActive ? 'border-primary' : '' }}"
                         role="button"
                         data-target="#{{ $panelId }}"
                         data-key="{{ $b['key'] }}"
                         tabindex="0">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <h5 class="card-title mb-0">
                                    {{ app()->getLocale() === 'en' ? $loc->show_name_en : $loc->show_name }}
                                </h5>
                            </div>
                            <p class="text-muted mb-3">
                                {{ __('messages.total_courses') ?? 'Courses' }}:
                                <span class="fw-bold">{{ count($courses) }}</span>
                            </p>
                            <span class="btn btn-outline-primary w-100">
                            {{ __('messages.view_courses') ?? 'View courses' }}
                        </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Panels (hidden until a card is clicked) --}}
        <div class="row">
            <div class="col-12">
                @foreach($blocks as $b)
                    @php
                        $loc = $b['location'] ?? null;
                        $courses = $b['courses'] ?? [];
                        if (!$loc) continue;

                        $panelId = 'panel-' . Str::slug($b['key']);
                        $isActive = $defaultKey === $b['key'];
                    @endphp

                    <div id="{{ $panelId }}" class="js-loc-panel {{ $isActive ? '' : 'd-none' }} mb-5">
                        @include('partials.course_table', ['location' => $loc, 'courses' => $courses])
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Minimal styles for active card highlight --}}
    <style>
        .js-loc-card:focus { outline: 0; box-shadow: 0 0 0 .2rem rgba(13,110,253,.25); }
        .js-loc-card.border-primary { border-width: 2px !important; }
    </style>

    {{-- Toggle logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cards  = document.querySelectorAll('.js-loc-card');
            const panels = document.querySelectorAll('.js-loc-panel');

            function showPanel(targetSelector) {
                panels.forEach(p => p.classList.add('d-none'));
                const target = document.querySelector(targetSelector);
                if (target) {
                    target.classList.remove('d-none');
                    // smooth scroll to the panel (nice on mobile)
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            function setActiveCard(activeCard) {
                cards.forEach(c => c.classList.remove('border-primary'));
                if (activeCard) activeCard.classList.add('border-primary');
            }

            cards.forEach(card => {
                const target = card.getAttribute('data-target');

                // click
                card.addEventListener('click', function () {
                    showPanel(target);
                    setActiveCard(card);
                    // update query string ?loc=key (no reload)
                    const key = card.getAttribute('data-key');
                    const url = new URL(window.location);
                    url.searchParams.set('loc', key);
                    window.history.replaceState({}, '', url);
                });

                // enter key support for accessibility
                card.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        card.click();
                    }
                });
            });
        });
    </script>

@endsection
