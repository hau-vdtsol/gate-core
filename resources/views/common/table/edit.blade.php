<form wire:submit.prevent="SaveForm" class="edit-{{ $module }} edit-form">
    @if (isset($option['formInclude']) && $option['formInclude'] != '')
        @include($option['formInclude'])
    @else
        <div class="p-1">
            {!! \GateGem\Core\Builder\Form\FormBuilder::Render($this, $option, ['isNew' => $isFormNew, 'errors' => $errors]) !!}
            <div class="text-center pt-3">
                <button class="btn btn-primary btn-sm" type="submit">
                    <i class="bi bi-download"></i> <span class="p-1">{{__("core::table.button.save")}}</span>
                </button>
                {!! apply_filters('module_edit_footer', '', $this) !!}
                {!! apply_filters('module_edit_' . $module . '_footer', '', $this) !!}
            </div>
        </div>
    @endif
</form>
