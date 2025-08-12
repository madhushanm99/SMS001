<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Report' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 18px; margin: 0 0 10px; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background: #f4f4f4; text-align: left; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 10px; color: #666; }
    </style>
  </head>
  <body>
    <h1>{{ $title ?? 'Report' }}</h1>
    <div class="muted">Period: {{ $dateFrom }} to {{ $dateTo }}</div>
    <table>
      <thead>
        <tr>
          @foreach(($columns ?? []) as $col)
            <th>{{ $col }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @forelse(($rows ?? []) as $row)
          <tr>
            @foreach($row as $cell)
              <td>{{ is_numeric($cell) ? number_format($cell, 2) : $cell }}</td>
            @endforeach
          </tr>
        @empty
          <tr><td colspan="{{ count($columns ?? []) }}" style="text-align:center;">No data</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="footer">Generated at {{ now()->format('Y-m-d H:i:s') }}</div>
  </body>
</html>


