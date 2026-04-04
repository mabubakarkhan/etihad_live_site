# Google Maps JavaScript API – localhost setup

## Fix RefererNotAllowedMapError (localhost not allowed)

1. Go to [Google Cloud Console](https://console.cloud.google.com/) → your project.
2. **APIs & Services** → **Credentials** → click your API key.
3. Under **Application restrictions** select **HTTP referrers (websites)**.
4. Click **Add an item** and add:
   - `http://localhost/etihad/public/*`  
   or (to allow any localhost):  
   - `http://localhost/*`
5. Click **Save**. Wait a minute, then reload your page.

Your page URL (e.g. `http://localhost/etihad/public/listing`) must match one of these patterns.

---

## 1. Create or use an API key

1. Open [Google Cloud Console](https://console.cloud.google.com/).
2. Select your project (or create one).
3. Go to **APIs & Services** → **Credentials**.
4. Click **Create credentials** → **API key**, or edit an existing key.

## 2. Enable required APIs

- **APIs & Services** → **Library**
- Enable **Maps JavaScript API**.
- If you use address autocomplete on the listing page, also enable **Places API**.

## 3. Allow your site URL (fix RefererNotAllowedMapError)

Your site URL must be in the key’s **HTTP referrers** list.

1. In **Credentials**, click your API key.
2. Under **Application restrictions**, choose **HTTP referrers (websites)**.
3. Add one or more of:
   - `http://localhost/etihad/public/*` – only this app
   - `http://localhost/*` – any localhost path
   - For production: `https://yourdomain.com/*`
4. Save.

The exact URL you see in the error (e.g. `http://localhost/etihad/public/listing`) must match one of these patterns. Using `http://localhost/etihad/public/*` or `http://localhost/*` covers it.

## 4. Use the key in this project

In `.env`:

```env
GOOGLE_MAPS_API_KEY=your_api_key_here
```

Optional (for advanced markers with a custom map style):

```env
GOOGLE_MAPS_MAP_ID=your_map_id_here
```

If you omit `GOOGLE_MAPS_MAP_ID`, the code uses `DEMO_MAP_ID` for development.

## 5. Script tag and initMap (working example)

**HTML container:**

```html
<div id="map" style="height:400px;width:100%"></div>
```

**Define `initMap` before the script tag** (Google calls it when the API is ready):

```html
<script>
function initMap() {
    const location = { lat: 31.5204, lng: 74.3587 };
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 12,
        center: location,
        mapId: "DEMO_MAP_ID"  // required for AdvancedMarkerElement
    });
    new google.maps.marker.AdvancedMarkerElement({
        map: map,
        position: location,
        title: "Location"
    });
}
window.initMap = initMap;
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=marker&callback=initMap">
</script>
```

- `async` + `defer`: load script in parallel, run after DOM ready.
- `callback=initMap`: global function name Google calls when the API is loaded.
- `libraries=marker`: required for `AdvancedMarkerElement` (replaces deprecated `google.maps.Marker`).
- For a full runnable example, open `public/theme/map-example.html` in the browser (after adding your URL to the API key referrers).
