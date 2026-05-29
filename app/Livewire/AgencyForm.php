<?php

namespace App\Livewire;

use App\Models\Agency;
use Livewire\Component;

class AgencyForm extends Component
{
    public string $name           = '';
    public string $type           = 'Government Hospital';
    public string $customType = '';
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
                        $knownTypes = ['Government Hospital', 'LGU', 'National Agency', 'SUC', 'GOCC', 'Other'];
            if (in_array($agency->type, $knownTypes)) {
                $this->type = $agency->type;
            } else {
                $this->type       = 'Other';
                $this->customType = $agency->type;
            }
            $this->region         = $agency->region ?? '';
            $this->contact_person = $agency->contact_person ?? '';
            $this->contact_email  = $agency->contact_email ?? '';
            $this->contact_phone  = $agency->contact_phone ?? '';
        }
    }

    public function save(): void
    {
 $knownTypes = ['Government Hospital', 'LGU', 'National Agency', 'SUC', 'GOCC'];
if ($this->type === 'Other') {
    if (trim($this->customType) === '') {
        $this->addError('customType', 'Please specify the agency type.');
        return;
    }
    if (in_array(trim($this->customType), $knownTypes)) {
        $this->addError('customType', 'That type already exists in the list. Please select it from the dropdown instead.');
        return;
    }
}

$this->validate([
    'name' => 'required|string|max:255|unique:agencies,name,' . ($this->agencyId ?? 'NULL'),
    'type'       => 'required|string',
    'customType' => 'nullable|string|max:255',
    'region' => 'nullable|string|max:255',
    'contact_person' => 'nullable|string|max:255|regex:/^[a-zA-Z\s\.\-]+$/',
    'contact_email'  => 'nullable|email|max:255',
    'contact_phone'  => 'nullable|string|max:50|regex:/^[0-9\+\-\(\)\s]+$/',
], [
    'name.unique'           => 'This agency already exists.',
    'customType.required_if' => 'Please specify the agency type.',
    'contact_person.regex'  => 'Contact person name should only contain letters, spaces, dots, and hyphens.',
    'contact_phone.regex'   => 'Contact phone should only contain numbers, spaces, +, -, and parentheses.',
]);

        $data = [
            'name'           => $this->name,
            'type' => $this->type === 'Other' ? $this->customType : $this->type,
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