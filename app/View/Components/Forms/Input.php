<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class Input extends Component
{
    public string $inputName;
    public string $inputId;
    public string $label;
    public mixed $value;
    public string $type = 'text';
    public ?array $errors = [];
    public ?array $options = [];
    public ?string $accept = null;
    public ?string $placeholder = null;
    public ?bool $valid = false;
    public ?bool $required = false;
    public ?string $validMessage = '';
    public ?float $step = 0.01;
    public ?float $numberMin = 0.0;
    public bool $multiple = false;
    public bool $disabled = false;
    public bool $showLabel = true;
    public bool $checked = false;

    public function __construct(string $inputName,string $inputId, string $label, mixed $value,?string $placeholder = null, ?string $type = 'text', ?array $errors = [], ?bool $valid = false,?bool $required = false,?string $validMessage = '', ?string $accept = null,?float $step = 0.01,?array $options = [],?float $numberMin = 0.0,?bool $multiple = false, ?bool $disabled = false, ?bool $showLabel = true, ?bool $checked = false)
    {
        $this->inputName = $inputName;
        $this->inputId = $inputId;
        $this->label = $label;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->type = $type;
        $this->errors = $errors;
        $this->accept = $accept;
        $this->valid = $valid;
        $this->validMessage = $validMessage;
        $this->required = $required;
        $this->step = $step;
        $this->options = $options;
        $this->numberMin = $numberMin;
        $this->multiple = $multiple;
        $this->disabled = $disabled;
        $this->showLabel = $showLabel;
        $this->checked = $checked;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.forms.input');
    }
}
