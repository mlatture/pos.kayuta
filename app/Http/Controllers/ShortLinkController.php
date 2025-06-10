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

        // Normalize source
        $source = strtolower(str_replace(' ', '', $request->source));
        if ($source === 'fb') {
            $source = 'facebook';
        }

        // Get base domain from settings
        $bookingBaseUrl = rtrim(BusinessSettings::where('type', 'booking_url')->value('value') ?? 'https://book.kayuta.com', '/');

        // Trim and sanitize the optional path
        $inputPath = trim($request->path ?? '');
        $cleanPath = trim($inputPath, '/');

        // Construct the URL path: either /{slug} or /{path}/{slug}
        $base = $bookingBaseUrl;
        if (!empty($cleanPath)) {
            $base .= '/' . $cleanPath;
        }
        $base .= '/' . $slug;

        // UTM parameters
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

        // Final full redirect URL with optional query
        $fullRedirectUrl = $base . (!empty($params) ? '?' . http_build_query($params) : '');

        $shortlink = ShortLink::create([
            'slug' => $slug,
            'path' => $cleanPath, // can be null or blank
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

        $bookingBaseUrl = rtrim(BusinessSettings::where('type', 'booking_url')->value('value') ?? 'https://book.kayuta.com', '/');

        $path = trim($shortlink->path ?? '', '/');

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $shortUrl = rtrim($path, '/') . '/' . $shortlink->slug;
        } else {
            $shortUrl = $bookingBaseUrl;
            // if (!empty($path)) {
            //     $shortUrl .= '/' ;
            // }
            $shortUrl .= '/go/' . $shortlink->slug;
        }

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
