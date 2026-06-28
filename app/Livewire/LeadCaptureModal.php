<?php

namespace App\Livewire;

use App\Models\Lead;
use Livewire\Component;

class LeadCaptureModal extends Component
{
    public bool $isOpen = false;

    public string $name = '';

    public string $whatsapp = '';

    public string $company = '';

    // WhatsApp settings - loaded from site settings
    public string $adminWhatsapp = '';

    public string $defaultMessage = 'Halo, saya tertarik dengan layanan Grapadi. Mohon informasi lebih lanjut.';

    protected $listeners = ['openLeadModal' => 'open'];

    public function mount(): void
    {
        $adminWhatsapp = site_setting('site_whatsapp', '6281234567890');

        if (! is_string($adminWhatsapp) && ! is_numeric($adminWhatsapp)) {
            $adminWhatsapp = '6281234567890';
        }

        $adminWhatsapp = trim((string) $adminWhatsapp);

        if ($adminWhatsapp === '') {
            $adminWhatsapp = '6281234567890';
        }

        $this->adminWhatsapp = $this->formatWhatsappNumber($adminWhatsapp);
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:100',
            'whatsapp' => 'required|string|min:10|max:20',
            'company' => 'nullable|string|max:100',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'name.min' => 'Nama minimal 2 karakter',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi',
            'whatsapp.min' => 'Nomor WhatsApp tidak valid',
        ];
    }

    public function open(): void
    {
        $this->reset(['name', 'whatsapp', 'company']);
        $this->resetValidation();
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function submit(): void
    {
        $this->validate();

        // Format WhatsApp number
        $formattedWhatsapp = $this->formatWhatsappNumber($this->whatsapp);

        // Create lead in CRM
        Lead::create([
            'name' => $this->name,
            'whatsapp' => $this->whatsapp,
            'company' => $this->company ?: null,
            'status' => 'new',
            'source' => 'web_wa',
            'notes' => 'Lead dari Web - WhatsApp Button',
        ]);

        // Generate WhatsApp URL with pre-filled message
        $message = urlencode($this->generateMessage());
        $waUrl = "https://wa.me/{$this->adminWhatsapp}?text={$message}";

        // Close modal and redirect to WhatsApp
        $this->isOpen = false;
        $this->dispatch('redirectToWhatsapp', url: $waUrl);
    }

    protected function formatWhatsappNumber(string $number): string
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        if (! str_starts_with($number, '62')) {
            $number = '62'.$number;
        }

        return $number;
    }

    protected function generateMessage(): string
    {
        $message = $this->defaultMessage;
        $message .= "\n\n---\n";
        $message .= "Nama: {$this->name}\n";
        $message .= "WhatsApp: {$this->whatsapp}\n";
        if ($this->company) {
            $message .= "Perusahaan: {$this->company}\n";
        }

        return $message;
    }

    public function render()
    {
        return view('livewire.lead-capture-modal');
    }
}
