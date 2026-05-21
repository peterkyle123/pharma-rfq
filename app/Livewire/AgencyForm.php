<?php

namespace App\Livewire;

use App\Models\Agency;
use Livewire\Component;

class AgencyForm extends Component
{
    public string $name           = '';
    public string $type           = 'Government Hospital';
    public string $region         = '';
    public string $contact_person = '';
    public string $contact_email  = '';
    public string $contact_phone  = '';

    public ?int $agencyId = null;

    public function mount(?int $agencyId = null): void
    {
        if ($agencyId) {
            $agency = Agency::findOrFail($agencyId);
            $this->agencyId       = $agency->id;
            $this->name           = $agency->name;
            $this->type           = $agency->type;
            $this->region         = $agency->region ?? '';
            $this->contact_person = $agency->contact_person ?? '';
            $this->contact_email  = $agency->contact_email ?? '';
            $this->contact_phone  = $agency->contact_phone ?? '';
        }
    }

    public function save(): void
    {
        $this->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|string',
            'region'         => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_email'  => 'nullable|email|max:255',
            'contact_phone'  => 'nullable|string|max:50',
        ]);

        $data = [
            'name'           => $this->name,
            'type'           => $this->type,
            'region'         => $this->region ?: null,
            'contact_person' => $this->contact_person ?: null,
            'contact_email'  => $this->contact_email ?: null,
            'contact_phone'  => $this->contact_phone ?: null,
        ];

        if ($this->agencyId) {
            Agency::findOrFail($this->agencyId)->update($data);
            $message = 'Agency updated successfully.';
        } else {
            Agency::create($data);
            $message = 'Agency added successfully.';
        }

        session()->flash('message', $message);
        $this->redirect(route('agencies.index'));
    }

    public function render()
    {
        return view('livewire.agency-form');
    }
}