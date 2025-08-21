<?php

namespace App\Livewire\PageWisePermission;

use App\Models\PageAndPermission;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CreatePageWisePermissionComponent extends Component
{




public $page_name;
public $operations = [];


public function storePagePermission(){

    $this->validate([
        'page_name' => 'required|unique:page_and_permissions,page_name',
        'operations' => 'nullable',
    ]);

    $data = new PageAndPermission();
    $data->page_name = $this->page_name;
    // $data->operations = $this->operations ?? null;
$data->operations = is_array($this->operations)
    ? json_encode($this->operations)
    : (is_string($this->operations) ? $this->operations : null);


    $data->save();

    $this->reset();
    return $this->redirect(route('page_wise_permission'), navigate: true);

}



    #[Layout('components.layouts.app.base')]
    public function render()
    {
        // $this->authorize('create', PageAndPermission::class);
        return view('livewire.page-wise-permission.create-page-wise-permission-component');
    }
}
