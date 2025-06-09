<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ShortLink;
use App\Models\BusinessSettings;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class ShortLinkController extends Controller
{
    public function index()
    {
        $shortlinks = ShortLink::all();
        return view('shortlinks.index', compact('shortlinks'));
    }

    public function create()
    {
        return view('shortlinks.createShortlinks');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|alpha_dash|unique:short_links,slug',
            'path' => 'nullable|string',
            'source' => 'nullable|string',
            'medium' => 'nullable|in:social,email,referral,banner,cpc',
            'campaign' => 'nullable|string',
        ]);

        $slug = Str::slug($request->slug);
        $source = strtolower(str_replace(' ', '', $request->source));
        if ($source === 'fb') {
            $source = 'facebook';
        }

        // 1. Get base booking URL
        $bookingBaseUrl = rtrim(BusinessSettings::where('type', 'booking_url')->value('value') ?? 'https://book.kayuta.com', '/');

        // 2. Handle input path
        $inputPath = trim($request->path ?? '');
        if ($inputPath === '') {
            $path = $bookingBaseUrl;
        } elseif (Str::startsWith($inputPath, ['http://', 'https://'])) {
            $path = rtrim($inputPath, '/');
        } else {
            $path = $bookingBaseUrl . '/' . ltrim($inputPath, '/');
        }

        // 3. Parse path into components
        $urlParts = parse_url($path);
        if (!$urlParts || !isset($urlParts['scheme']) || !isset($urlParts['host'])) {
            return response()->json(['message' => 'Invalid URL or missing scheme/host.'], 422);
        }

        // 4. Build UTM parameters
        $params = [];
        if ($source) {
            $params['utm_source'] = $source;
        }
        if ($request->medium && $request->medium !== 'none') {
            $params['utm_medium'] = $request->medium;
        }
        if ($request->campaign) {
            $params['utm_campaign'] = $request->campaign;
        }

        // 5. Merge existing query params with UTM
        parse_str($urlParts['query'] ?? '', $existingQuery);
        $mergedQuery = array_merge($existingQuery, $params);

        // 6. Reconstruct full URL
        $base = $urlParts['scheme'] . '://' . $urlParts['host'] . ($urlParts['path'] ?? '');
        $fullRedirectUrl = $base . (!empty($mergedQuery) ? '?' . http_build_query($mergedQuery) : '');

        // 7. Save
        $shortlink = ShortLink::create([
            'slug' => $slug,
            'path' => $path,
            'source' => $source,
            'medium' => $request->medium,
            'campaign' => $request->campaign,
            'fullredirecturl' => $fullRedirectUrl,
        ]);

        return response()->json([
            'redirect_url' => route('shortlinks.show', $shortlink->id),
        ]);

    }

    public function show($id)
    {
        $shortlink = ShortLink::findOrFail($id);
        $shortUrl = rtrim($shortlink->path, '/') . '/go/' . $shortlink->slug;
        $qr = QrCode::format('png')->size(300)->generate($shortUrl);

        return view('shortlinks.show', compact('shortlink', 'shortUrl', 'qr'));
    }

    public function redirect($slug)
    {
        $shortlink = ShortLink::where('slug', $slug)->first();

        if (!$shortlink) {
            // fallback: create placeholder record
            $bookingUrl = business_setting('booking_url');
            ShortLink::create([
                'slug' => $slug,
                'path' => null,
                'clicks' => 1,
                'fullredirecturl' => $bookingUrl,
            ]);
            return redirect()->to($bookingUrl);
        }

        $shortlink->increment('clicks');
        return redirect()->to($shortlink->fullredirecturl);
    }

    public function edit($id)
    {
        $shortlink = ShortLink::findOrFail($id);
        return view('shortlinks.edit', compact('shortlink'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'slug' => 'required|alpha_dash|unique:short_links,slug,' . $id,
            'path' => 'nullable|string',
            'source' => 'nullable|string',
            'medium' => 'nullable|in:social,email,referral,banner,cpc',
            'campaign' => 'nullable|string',
        ]);

        $shortlink = ShortLink::findOrFail($id);

        $slug = Str::slug($request->slug);
        $path = $request->path ?: BusinessSettings::where('type', 'booking_url')->value('value');
        $path = rtrim($path, '/');

        if (!filter_var($path, FILTER_VALIDATE_URL)) {
            return back()
                ->withErrors(['path' => 'Invalid URL'])
                ->withInput();
        }

        $source = strtolower(str_replace(' ', '', $request->source));
        if ($source === 'fb') {
            $source = 'facebook';
        }

        $params = [];
        if ($source) {
            $params['utm_source'] = $source;
        }
        if ($request->medium && $request->medium !== 'none') {
            $params['utm_medium'] = $request->medium;
        }
        if ($request->campaign) {
            $params['utm_campaign'] = $request->campaign;
        }

        $urlParts = parse_url($path);
        $base = $urlParts['scheme'] . '://' . $urlParts['host'] . ($urlParts['path'] ?? '');
        parse_str($urlParts['query'] ?? '', $existingQuery);
        $mergedQuery = array_merge($existingQuery, $params);
        $fullRedirectUrl = $base . '?' . http_build_query($mergedQuery);

        $shortlink->update([
            'slug' => $slug,
            'path' => $path,
            'source' => $source,
            'medium' => $request->medium,
            'campaign' => $request->campaign,
            'fullredirecturl' => $fullRedirectUrl,
        ]);

        return redirect()->route('shortlinks.show', $shortlink->id)->with('success', 'Shortlink updated successfully!');
    }

    public function destroy($id)
    {
        $shortlink = ShortLink::findOrFail($id);
        $shortlink->delete();

        return redirect()->route('shortlinks.index')->with('success', 'Shortlink deleted successfully.');
    }
}
