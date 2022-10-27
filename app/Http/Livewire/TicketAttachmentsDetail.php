<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Ticket;
use Livewire\Component;
use App\Models\Attachment;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketAttachmentsDetail extends Component
{
    use WithPagination;
    use WithFileUploads;
    use AuthorizesRequests;

    public Ticket $ticket;
    public Attachment $attachment;
    public $usersForSelect = [];
    public $attachmentAttachment;
    public $uploadIteration = 0;

    public $selected = [];
    public $editing = false;
    public $allSelected = false;
    public $showingModal = false;

    public $modalTitle = 'New Attachment';

    protected $rules = [
        'attachmentAttachment' => ['file', 'max:1024', 'required'],
        'attachment.description' => ['nullable', 'max:255', 'string'],
        'attachment.user_id' => ['required', 'exists:users,id'],
    ];

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->usersForSelect = User::pluck('name', 'id');
        $this->resetAttachmentData();
    }

    public function resetAttachmentData()
    {
        $this->attachment = new Attachment();

        $this->attachmentAttachment = null;
        $this->attachment->user_id = null;

        $this->dispatchBrowserEvent('refresh');
    }

    public function newAttachment()
    {
        $this->editing = false;
        $this->modalTitle = trans('crud.ticket_attachments.new_title');
        $this->resetAttachmentData();

        $this->showModal();
    }

    public function editAttachment(Attachment $attachment)
    {
        $this->editing = true;
        $this->modalTitle = trans('crud.ticket_attachments.edit_title');
        $this->attachment = $attachment;

        $this->dispatchBrowserEvent('refresh');

        $this->showModal();
    }

    public function showModal()
    {
        $this->resetErrorBag();
        $this->showingModal = true;
    }

    public function hideModal()
    {
        $this->showingModal = false;
    }

    public function save()
    {
        $this->validate();

        if (!$this->attachment->ticket_id) {
            $this->authorize('create', Attachment::class);

            $this->attachment->ticket_id = $this->ticket->id;
        } else {
            $this->authorize('update', $this->attachment);
        }

        if ($this->attachmentAttachment) {
            $this->attachment->attachment = $this->attachmentAttachment->store(
                'public'
            );
        }

        $this->attachment->save();

        $this->uploadIteration++;

        $this->hideModal();
    }

    public function destroySelected()
    {
        $this->authorize('delete-any', Attachment::class);

        collect($this->selected)->each(function (string $id) {
            $attachment = Attachment::findOrFail($id);

            if ($attachment->attachment) {
                Storage::delete($attachment->attachment);
            }

            $attachment->delete();
        });

        $this->selected = [];
        $this->allSelected = false;

        $this->resetAttachmentData();
    }

    public function toggleFullSelection()
    {
        if (!$this->allSelected) {
            $this->selected = [];
            return;
        }

        foreach ($this->ticket->attachments as $attachment) {
            array_push($this->selected, $attachment->id);
        }
    }

    public function render()
    {
        return view('livewire.ticket-attachments-detail', [
            'attachments' => $this->ticket->attachments()->paginate(20),
        ]);
    }
}
