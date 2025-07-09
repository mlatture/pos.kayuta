<pre
    style="font-size: 0.75rem; background:#f8f9fa; border:1px solid #ddd; border-radius:4px; padding:6px; max-height:120px; overflow:auto;">
    @foreach ($data as $key => $value)
{{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}
@endforeach
    </pre>
