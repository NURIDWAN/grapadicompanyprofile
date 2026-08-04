<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'asset_id',
        'whatsapp',
        'company',
        'status',
        'source',
        'converted_to_client_id',
        'converted_at',
        'value',
        'notes',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'converted_at' => 'datetime',
    ];

    public const STATUS_OPTIONS = [
        'new' => 'New Lead',
        'contacted' => 'Contacted',
        'meeting' => 'Meeting',
        'proposal' => 'Proposal Sent',
        'negotiation' => 'Negotiation',
        'deal' => 'Deal Won',
        'rejected' => 'Rejected',
    ];

    public const SOURCE_OPTIONS = [
        'web_wa' => 'Web WhatsApp',
        'manual' => 'Manual Entry',
        'referral' => 'Referral',
        'social' => 'Social Media',
        'event' => 'Event',
        'asset_matching' => 'Asset Matching',
    ];

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'info',
            'contacted' => 'warning',
            'meeting' => 'primary',
            'proposal' => 'purple',
            'negotiation' => 'pink',
            'deal' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    public function getSourceColorAttribute(): string
    {
        return match ($this->source) {
            'web_wa' => 'success',
            'manual' => 'gray',
            'referral' => 'info',
            'social' => 'purple',
            'event' => 'warning',
            'asset_matching' => 'primary',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? $this->status;
    }

    public function getSourceLabelAttribute(): string
    {
        return self::SOURCE_OPTIONS[$this->source] ?? $this->source;
    }

    /**
     * Client yang dikonversi dari lead ini
     */
    public function convertedToClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'converted_to_client_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Semua interaksi dengan lead
     */
    public function interactions(): MorphMany
    {
        return $this->morphMany(Interaction::class, 'interactable');
    }

    /**
     * Check if lead is converted
     */
    public function getIsConvertedAttribute(): bool
    {
        return ! is_null($this->converted_to_client_id);
    }

    /**
     * Format WhatsApp number to international format
     */
    public function getFormattedWhatsappAttribute(): ?string
    {
        if (! $this->whatsapp) {
            return null;
        }

        $number = preg_replace('/[^0-9]/', '', $this->whatsapp);

        // Convert 08xx to 628xx
        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        // Add 62 if not present
        if (! str_starts_with($number, '62')) {
            $number = '62'.$number;
        }

        return $number;
    }

    /**
     * Generate WhatsApp chat URL
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        if (! $this->formatted_whatsapp) {
            return null;
        }

        return "https://wa.me/{$this->formatted_whatsapp}";
    }

    /**
     * Convert lead to client
     */
    public function convertToClient(): Client
    {
        $client = Client::create([
            'company_name' => $this->company ?: $this->name,
            'pic_name' => $this->name,
            'pic_phone' => $this->whatsapp,
            'contract_value' => $this->value,
            'converted_from_lead_id' => $this->id,
            'notes' => $this->notes,
            'status' => 'active',
        ]);

        $this->update([
            'status' => 'deal',
            'converted_to_client_id' => $client->id,
            'converted_at' => now(),
        ]);

        return $client;
    }
}
