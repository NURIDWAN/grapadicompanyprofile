<?php

namespace App\Notifications;

use App\Enums\AssetStatus;
use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssetReviewResultNotification extends Notification
{
    use Queueable;

    public function __construct(public Asset $asset) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $published = $this->asset->status === AssetStatus::Published;

        return (new MailMessage)
            ->subject($published ? 'Aset Anda Telah Dipublikasikan' : 'Aset Anda Memerlukan Revisi')
            ->greeting('Halo '.$notifiable->name.',')
            ->line($published
                ? "Aset {$this->asset->name} telah lolos screening dasar dan dipublikasikan di katalog Grapadi Asset Matching."
                : "Aset {$this->asset->name} memerlukan revisi sebelum dapat dipublikasikan.")
            ->when($this->asset->latest_review_notes, fn (MailMessage $mail) => $mail->line('Catatan Grapadi: '.$this->asset->latest_review_notes))
            ->action('Buka Dashboard', route('matching.dashboard'))
            ->line('Screening ini bukan valuasi, appraisal, studi kelayakan, maupun financial model.');
    }

    public function toArray(object $notifiable): array
    {
        $published = $this->asset->status === AssetStatus::Published;

        return [
            'asset_id' => $this->asset->id,
            'title' => $published ? 'Aset dipublikasikan' : 'Aset perlu revisi',
            'message' => $published ? "{$this->asset->name} telah tampil di katalog." : ($this->asset->latest_review_notes ?: 'Silakan perbarui data aset Anda.'),
            'url' => $published ? route('matching.show', $this->asset) : route('matching.assets.edit', $this->asset),
        ];
    }
}
