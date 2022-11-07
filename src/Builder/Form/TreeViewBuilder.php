<?php

namespace LaraPlatform\Core\Builder\Form;

use Illuminate\Support\Arr;
use LaraPlatform\Core\Builder\HtmlBuilder;

class TreeViewBuilder extends HtmlBuilder
{
    public $option;
    public $data;
    public $formData;
    public function __construct($option, $data, $formData)
    {
        $this->option = $option;
        $this->data = $data;
        $this->formData = $formData;
    }
    public function getModelField($value)
    {
        if (getValueByKey($this->formData, 'filter', false)) {
            return 'wire:model.lazy="' . getValueByKey($this->formData, 'prex', '') . $this->option['field'] . '"';
        }
        return (getValueByKey($this->option, 'defer', true) ? 'wire:model.defer' : 'wire:model') . '="' . getValueByKey($this->formData, 'prex', '')  . $this->option['field'] . '.' . $value . '"';
    }
    private function TreeRender($data, $treeLevel = 0)
    {
        $gropData =  groupBy($data, function ($item) use ($treeLevel) {
            if (strlen($item['key']) < $treeLevel) return $item['key'];
            $pos =  strpos($item['key'], ".", $treeLevel);
            if (!$pos) return $item['key'];
            return substr($item['key'], 0, $pos);
        });
        if (count($gropData) == 0) return;
        ksort($gropData, SORT_STRING);
        echo "<ul>";
        foreach ($gropData as $key => $items) {
            if ($treeLevel == 0) {
                echo '<li class="show">';
            } else {
                echo '<li>';
            }

            if (count($items) == 1) {
                echo '<div class="form-check  ms-4">
                <input type="checkbox" value="' . $items[0]['value'] . '" ' . (getValueByKey($this->option, 'attr', '')) . ' class="form-check-input" id="cbk_id_' . $this->option['field'] . '_' . $items[0]['value'] . '" ' .  $this->getModelField($items[0]['value']) . '/>
                <label class="form-check-label" for="cbk_id_' . $this->option['field'] . '_' . $items[0]['value'] . '">' . $items[0]['text'] . '</label>
                </div>';
            } else {
                echo '<i class="bi bi-chevron-down"></i>
                <i class="bi bi-chevron-right"></i>
                <div class="d-inline-block form-check ms-1">
                <input type="checkbox" ' . (getValueByKey($this->option, 'attr', '')) . ' class="form-check-input cbk_root cbk_' . $this->option['field'] . '_' . $key . '" tree-root="cbk_' . $this->option['field'] . '_' . $key . '" id="cbk_id_' . $this->option['field'] . '_' . $key . '"/>
                <label class="form-check-label" for="cbk_id_' . $this->option['field'] . '_' . $key . '">' . $key . '</label>
                </div>';
                $this->TreeRender($items, strlen($key) + 1);
            }
            echo "</li>";
        }
        echo "</ul>";
    }
    public function RenderHtml()
    {
        $funcData = getValueByKey($this->option, 'funcData', null);
        if ($funcData && is_array($funcData)) {
        } else if ($funcData) {
            $funcData = $funcData();
        }
        if ($funcData) {
            echo '<div class="tree-view form-tree" id="input-' . $this->option['field'] . '">';
            $this->TreeRender($funcData, 0);
            echo '</div>';
        }
    }
    public static function Render($data, $option,  $formData)
    {
        return (new self($data, $option, $formData))->ToHtml();
    }
}