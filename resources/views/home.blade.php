<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalonSync</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; color: #333; background: #fafafa; }
        header { background: #7b2d5f; color: #fff; padding: 16px 32px; font-size: 22px; font-weight: bold; }
        .hero { background: #f3e3ee; text-align: center; padding: 64px 24px; }
        .hero h1 { margin: 0 0 12px; font-size: 36px; color: #7b2d5f; }
        .hero p { max-width: 600px; margin: 0 auto; font-size: 18px; }
        section { max-width: 960px; margin: 40px auto; padding: 0 24px; }
        h2 { color: #7b2d5f; }
        .cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
        .card { background: #fff; border: 1px solid #e5d5df; border-radius: 8px; padding: 16px; }
        .card h3 { margin-top: 0; }
        .price { color: #7b2d5f; font-weight: bold; }
        .warning { background: #fff3cd; padding: 12px 24px; text-align: center; }
        footer { text-align: center; padding: 24px; color: #888; }
        .btn-light { background: #fff; color: #7b2d5f; padding: 9px 22px; border-radius: 999px; text-decoration: none; font-weight: bold; font-size: 16px; }
        .btn-light:hover { background: #f3e3ee; }
        .btn-outline { background: transparent; color: #fff; border: 2px solid #fff; padding: 7px 22px; border-radius: 999px; text-decoration: none; font-weight: bold; font-size: 16px; font-family: inherit; cursor: pointer; }
        .btn-outline:hover { background: #fff; color: #7b2d5f; }
    </style>
</head>
<body>
            <header style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('home') }}" style="display: flex; align-items: center; gap: 10px; color: #fff; text-decoration: none;">
            <x-application-logo style="height: 40px; width: 40px;" />
            <span>SalonSync</span>
        </a>

        <nav style="display: flex; align-items: center; gap: 12px; font-size: 16px; font-weight: bold;">
            <a href="#pricing" style="color: #fff; text-decoration: none; margin-right: 8px;">Pricing</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-light">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-outline">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-outline">Log in</a>
                <a href="{{ route('register') }}" class="btn-light">Sign up</a>
            @endauth
        </nav>
    </header>

    @unless($cmsOnline)
        <div class="warning">The content server is currently unavailable. Please try again later.</div>
    @endunless

    <div class="hero">
        <h1>{{ $home['hero_title'] ?? 'Welcome to SalonSync' }}</h1>
        <p>{{ $home['hero_subtitle'] ?? '' }}</p>
                <p style="margin-top: 24px;">
            <a href="{{ route('appointments.create') }}"
               style="background: #7b2d5f; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none;">
                Book an Appointment
            </a>
        </p>
    </div>

    <section>
        <h2>Latest Announcements</h2>
        @forelse($announcements as $announcement)
            <div class="card" style="margin-bottom: 12px;">
                <h3>{{ $announcement['title'] }}</h3>
                <p>{!! nl2br(e($announcement['text'])) !!}</p>
            </div>
        @empty
            <p>No announcements yet.</p>
        @endforelse
    </section>

    <section>
        <h2>Our Services</h2>
        <div class="cards">
            @forelse($services as $service)
                <div class="card">
                    <h3>{{ $service['name'] }}</h3>
                    <p>{{ $service['description'] }}</p>
                    <p class="price">₱{{ number_format($service['price'], 2) }}</p>
                    <small>{{ $service['duration_minutes'] }} minutes</small>
                </div>
            @empty
                <p>No services available.</p>
            @endforelse
        </div>
    </section>

        <section id="pricing">
        <h2>Subscription Plans for Salons</h2>
        <div class="cards">
            @forelse($plans as $plan)
                <div class="card" style="{{ ($plan['is_featured'] ?? false) ? 'border: 2px solid #7b2d5f;' : '' }}">
                    @if($plan['is_featured'] ?? false)
                        <small class="price">MOST POPULAR</small>
                    @endif
                    <h3>{{ $plan['name'] }}</h3>
                    <p class="price" style="font-size: 24px;">
                        ₱{{ number_format($plan['price'], 2) }}
                        <small style="color: #888; font-weight: normal;">/ month</small>
                    </p>
                    <p>{{ $plan['description'] ?? '' }}</p>
                    <ul>
                        @foreach(array_filter(array_map('trim', explode("\n", $plan['features'] ?? ''))) as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('subscriptions.create', $plan['documentId']) }}"
                       style="display: inline-block; background: #7b2d5f; color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
                        Subscribe
                    </a>
                </div>
            @empty
                <p>No plans available.</p>
            @endforelse
        </div>
    </section>
    
    <footer>&copy; SalonSync</footer>
</body>
</html>