<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'price',
        'stock',
        'description',
        'image',
        'is_active',
        'is_trending',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_trending' => 'boolean',
    ];

    /**
     * Generate dynamic encrypted hash for an ID.
     * Uses random salt/IV so it produces a different hash every time,
     * formatted as 8-4-4-4-12 hex string with HMAC integrity check.
     */
    public static function encodeDynamicHash(int|string $id): string
    {
        $appKey = hash('sha256', config('app.key'), true);
        $iv = random_bytes(6); // 6 bytes random salt
        $idBytes = pack('N', (int) $id); // 4 bytes uint32

        // Encrypt ID with keystream
        $streamKey = hash_hmac('sha256', $iv, $appKey, true);
        $cipher = $idBytes ^ substr($streamKey, 0, 4);

        // 6 bytes HMAC signature
        $mac = substr(hash_hmac('sha256', $iv . $cipher, $appKey, true), 0, 6);

        // Total 16 bytes = 32 hex characters formatted as 8-4-4-4-12
        $hex = bin2hex($iv . $cipher . $mac);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($hex, 4));
    }

    /**
     * Decode dynamic encrypted hash back to original ID.
     */
    public static function decodeDynamicHash(string $token): ?int
    {
        $hex = str_replace('-', '', $token);
        if (strlen($hex) !== 32 || !ctype_xdigit($hex)) {
            return null;
        }

        $binary = hex2bin($hex);
        $iv = substr($binary, 0, 6);
        $cipher = substr($binary, 6, 4);
        $mac = substr($binary, 10, 6);

        $appKey = hash('sha256', config('app.key'), true);
        $expectedMac = substr(hash_hmac('sha256', $iv . $cipher, $appKey, true), 0, 6);
        if (!hash_equals($expectedMac, $mac)) {
            return null;
        }

        $streamKey = hash_hmac('sha256', $iv, $appKey, true);
        $idBytes = $cipher ^ substr($streamKey, 0, 4);
        $unpacked = unpack('N', $idBytes);

        return $unpacked ? (int) $unpacked[1] : null;
    }

    /**
     * Return dynamic hash when model is passed to route().
     * Every call generates a fresh, unique hash.
     */
    public function getRouteKey(): mixed
    {
        return static::encodeDynamicHash($this->id);
    }

    /**
     * Resolve model instance from route parameter (dynamic hash or fallback numeric ID).
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return parent::resolveRouteBinding($value, $field);
        }

        // Try decoding dynamic hash
        $id = static::decodeDynamicHash((string) $value);

        // Fallback to numeric ID if not a hash
        if (!$id && is_numeric($value)) {
            $id = (int) $value;
        }

        if (!$id) {
            abort(404);
        }

        return $this->where('id', $id)->firstOrFail();
    }
}
