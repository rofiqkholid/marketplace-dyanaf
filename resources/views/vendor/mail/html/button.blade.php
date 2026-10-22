@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto;">
<tr>
<td style="border-radius: 9999px; background: #9333ea; background: linear-gradient(135deg, #9333ea 0%, #c026d3 50%, #f43f5e 100%);">
<a href="{{ $url }}" target="_blank" rel="noopener" style="display: inline-block; padding: 12px 28px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 13px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 9999px; background: #9333ea; background: linear-gradient(135deg, #9333ea 0%, #c026d3 50%, #f43f5e 100%); letter-spacing: 0.3px;">
{!! $slot !!}
</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
