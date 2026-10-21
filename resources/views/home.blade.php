<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SalonSync</title>
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
    </style>
</head>
<body>
    <header>SalonSync</header>

    @unless($cmsOnline)
        <div class="warning">The content server is currently unavailable. Please try again later.</div>
    @endunless

    <div class="hero">
        <h1>{{ $home['hero_title'] ?? 'Welcome to SalonSync' }}</h1>
        <p>{{ $home['hero_subtitle'] ?? '' }}</p>
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

    <footer>&copy; SalonSync</footer>
</body>
</html>