@if($type != 'checkbox' and $type != 'radio')
    <div @class(['form-group','mb-3']) >
        @if($showLabel)
            <label for="{{$inputId}}">{{$label}} @if($required) <span class="text-danger" >*</span> @endif</label>
        @endif
        @if($type != 'textarea' and $type != 'select' and $type != 'password' and $type != 'checkbox')
            <input type="{{$type}}" name="{{$inputName}}" placeholder="{{$placeholder}}" value="{{$value}}" @class(['form-control','is-invalid' => count($errors) > 0,'is-valid' => $valid == true]) id="{{$inputId}}" {{$required ? "required" : ""}} step="{{$step}}" min="{{$numberMin}}" accept="{{$accept}}" @disabled($disabled) />
        @elseif($type === 'password')
            <div class="input-group">
                <input type="{{$type}}" name="{{$inputName}}" placeholder="{{$placeholder}}" value="{{$value}}" @class(['form-control','is-invalid' => count($errors) > 0,'is-valid' => $valid == true]) id="{{$inputId}}" {{$required ? "required" : ""}} step="{{$step}}" min="{{$numberMin}}" accept="{{$accept}}" @disabled($disabled) />
                <div class="input-group-append" onclick="showPassword('{{$inputId}}')" >
                    <span class="input-group-text"><i class="fa fa-eye" ></i></span>
                </div>
            </div>
        @elseif($type == 'textarea')
            <textarea maxlength="300" rows="5" name="{{$inputName}}" id="{{$inputId}}" @class(['form-control','is-invalid' => count($errors) > 0,'is-valid' => $valid]) placeholder="{{$placeholder}}" @disabled($disabled) >{{$value}}</textarea>
        @else
            @if(!$multiple)
                <select name="{{$inputName}}" id="{{$inputId}}" @class(['form-control','select2-input','is-invalid' => count($errors) > 0,'is-valid' => $valid]) {{$required ? "required" : ""}} @disabled($disabled)  >
                    <option value="" >{{$placeholder}}</option>
                    @foreach($options as $option)
                        <option {{ $option['value'] == $value ? "selected" : "" }} value="{{ $option['value'] }}">{{$option['label']}}</option>
                    @endforeach
                </select>
            @else
                <select data-placeholder="{{$placeholder}}" name="{{$inputName}}" id="{{$inputId}}" @class(['select2','form-control','select2-multiple','select2-input','is-invalid' => count($errors) > 0,'is-valid' => $valid]) {{$required ? "required" : ""}} multiple @disabled($disabled)  >
                    @foreach($options as $option)
                        <option {{ (is_array($value) and in_array($option['value'],$value)) ? "selected" : "" }} value="{{ $option['value'] }}">{{$option['label']}}</option>
                    @endforeach
                </select>
            @endif
        @endif
        <div class="invalid-feedback d-block">
            {!! implode("<br/>",$errors) !!}
        </div>
        <div class="valid-feedback">
            {{$validMessage}}
        </div>
    </div>
@elseif($type == 'checkbox')
    <div class="form-check">
        <input class="form-check-input" type="checkbox" {{(($checked) ? "checked" : "")}} value="{{$value}}" id="{{$inputId}}" name="{{$inputName}}" >
        <label class="form-check-label" for="{{$inputId}}">
            {{ $label }}
        </label>
    </div>
@else
    <div class="form-check">
        <input class="form-check-input" type="radio" name="{{$inputName}}" id="{{$inputId}}" value="{{$value}}" {{($checked) ? "checked" : ""}}>
        <label class="form-check-label" for="{{$inputId}}">
            {{$label}}
        </label>
    </div>
@endif
