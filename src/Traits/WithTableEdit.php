<?php

namespace GateGem\Core\Traits;

use GateGem\Core\Livewire\Modal;
use GateGem\Core\Loader\TableLoader;
use Livewire\WithFileUploads;

trait WithTableEdit
{
    use WithFileUploads;
    public $module = '';
    public $dataId = 0;
    public $isFormNew = true;
    public $rules = [];
    public function mount()
    {
        return $this->LoadData();
    }
    protected function getView()
    {
        return 'core::common.table.edit';
    }
    public function getOptionProperty()
    {
        if (method_exists($this, "getOption")) return $this->getOption();
        return TableLoader::getDataByKey($this->module);
    }
    public function getFieldsProperty()
    {
        return  getValueByKey($this->getOptionProperty(), 'fields', []);
    }
    public function LoadData()
    {
        $option = $this->getOptionProperty();
        if (!$option || !isset($option['model']) || $option['model'] == '')
            return abort(404);

        if (!$this->modal_isPage) {
            $this->modal_size = getValueByKey($option, 'modal_size',  Modal::FullscreenMd);
        }
        $this->setTitle(__(getValueByKey($option, 'title', 'core::tables.' . $this->module . '.title')));
        $fields = $this->getFieldsProperty();
        $data = null;
        if ($this->dataId) {
            // edit
            $data = app($option['model'])->find($this->dataId);
            if (!$data)
                return abort(404);
            $this->isFormNew = false;
        } else {
            // new
            $data = new (app($option['model']));
        }
        foreach ($fields as $item) {
            if (isset($item['field']) && $item['field'] != '') {
                if (isset($data->{$item['field']}))
                    $this->{$item['field']} = $data->{$item['field']};
                else {
                    if ($this->isFormNew) {
                        $default_value = getValueByKey($item, 'default', '');
                        if (is_callable($default_value))
                            $this->{$item['field']} = $default_value($this->isFormNew);
                        else
                            $this->{$item['field']} = $default_value;
                    } else {
                        $default_value = getValueByKey($item, 'default', '');
                        if (is_callable($default_value))
                            $this->{$item['field']} = $default_value($this->isFormNew);
                        else
                            $this->{$item['field']} = '';
                    }
                }
            }
        }
        $fnRule = getValueByKey($option, 'formRule', null);
        if ($fnRule) {
            $this->rules = $fnRule($this->dataId, $this->isFormNew) ?? [];
        }
        $fnRuleMessages = getValueByKey($option, 'ruleMessages', null);
        if ($fnRuleMessages) {
            $this->messages = $fnRuleMessages($this->dataId, $this->isFormNew) ?? [];
        }
        do_action("module_edit_loaddata", $this->module, $this);
        do_action("module_edit_" . $this->module . "_loaddata", $this);
    }
    public function LoadModule($module, $dataId = null)
    {
        $this->dataId = $dataId;
        if (!$module) return abort(404);
        $this->module = $module;
        $this->_code_permission = 'core.' . $this->module . ($dataId ? '.edit' : '.add');
        if (!$this->checkPermissionView())
            return abort(403);
        $this->LoadData();
    }
    public function SaveForm()
    {
        if ($this->rules && count($this->rules) > 0)
            $this->validate();

        $option = $this->getOptionProperty();
        $data = null;
        if ($this->dataId) {
            // edit
            $data = app($option['model'])->find($this->dataId);
            if (!$data)
                return abort(404);
            $this->isFormNew = false;
        } else {
            // new
            $data = new (app($option['model']));
        }
        $fields = $this->getFieldsProperty();
        if (method_exists($this, 'beforeBinding')) {
            $this->beforeBinding();
        }
        foreach ($fields as $item) {
            if (isset($item['field']) && $item['field'] != '') {
                $valuePreview = $this->{$item['field']};
                if ($valuePreview && $valuePreview instanceof \Illuminate\Http\UploadedFile) {
                    if (isset($item['imageFolder']) && $item['imageFolder'] != '')
                        $valuePreview = $valuePreview->store('public/' . $item['imageFolder']);
                    else
                        $valuePreview = $valuePreview->store('public');
                    $valuePreview = str_replace('public', 'storage', $valuePreview);
                }
                $data->{$item['field']} =  $valuePreview;
            }
        }
        if (method_exists($this, 'beforeSave')) {
            $this->beforeSave();
        }
        $data->save();
        $this->refreshData(['module' => $this->module]);
        $this->hideModal();
        $this->ShowMessage('Update successful!');
    }
    public function render()
    {
        return $this->viewModal($this->getView(), [
            'option' => $this->option,
            'fields' => $this->fields
        ]);
    }
    public function CheckNullAndEmptySetValue($arrayField, $default)
    {
        foreach ($arrayField as $field) {
            if (isset($this->{$field}) && ($this->{$field} == null || $this->{$field} == '')) {
                $this->{$field} = $default;
            }
        }
    }
}
