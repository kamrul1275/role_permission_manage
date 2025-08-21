<?php

namespace App\Livewire\PageWisePermission;

use App\Models\PageAndPermission;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EditPageWisePermission extends Component
{
    public $pageId;
    public $page_name;
    public $operations;

    public function mount($id)
    {
        $page = PageAndPermission::findOrFail($id);

        $this->pageId = $page->id;
        $this->page_name = $page->page_name;
        $this->operations = is_array($page->operations)
            ? json_encode($page->operations)
            : $page->operations; // If stored as JSON string
    }

    public function updatePagePermission()
    {
        $this->validate([
            'page_name' => 'required|unique:page_and_permissions,page_name,' . $this->pageId,
            // 'operations' => 'required',
        ]);

        $page = PageAndPermission::findOrFail($this->pageId);

        $page->page_name = $this->page_name;

        // Try to decode JSON, fallback to comma-separated
        $decoded = json_decode($this->operations, true);
        $page->operations = $decoded ?? array_map('trim', explode(',', $this->operations));

        $page->save();

        return $this->redirect(route('page_wise_permission'), navigate: true);
    }
    #[Layout('components.layouts.app.base')]
    public function render()
    {
        return view('livewire.page-wise-permission.edit-page-wise-permission');
    }
}
