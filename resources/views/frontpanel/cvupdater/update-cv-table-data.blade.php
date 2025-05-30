@php
    $i = 0;
    $c = 0;
@endphp

<table class="table table-hover cvTableImport">
    <thead>
        <tr>
            <th>Add</th>
            <th>Name</th>
            <th>Custom Field To Map
                <i class="bi bi-question-circle-fill" data-toggle="tooltip" title="Select your custom field to map to your custom value."></i>
            </th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cv['customValues'] as $cvValue)
        @php
            $i++;
            $color = ($c == 0) ? 'background:#e1e1e1;' : 'background:#fff;';
            $c = 1 - $c;

            // Find if this custom value exists in our collection
            $existingCv = collect($cv['db'])->firstWhere('mergeKey', $cvValue->fieldKey);

            $selected = $existingCv ? 'checked' : '';
            $tooltip = $existingCv ? $existingCv['tooltip'] : '';
            $fieldtype = $existingCv ? $existingCv['fieldType'] : 'text';
            $resource = $existingCv ? $existingCv['resources'] : '';
            $readonly = $existingCv && $existingCv['cvaction'] === 'readonly' ? 'checked' : '';
            $wysiwyg = $existingCv && $existingCv['cvattribute'] === 'wysiwyg' ? 'checked' : '';
            $customField = $existingCv ? $existingCv['custom_field'] : '';
            $sort = $existingCv ? $existingCv['cv_order'] : 0;
            $default = $existingCv ? $existingCv['defaultv'] : '';
            $cvId = $existingCv ? $existingCv['id'] : '';

            $ftText = $fieldtype === 'text' ? 'selected' : '';
            $ftBoolean = $fieldtype === 'boolean' ? 'selected' : '';
            $ftPar = $fieldtype === 'paragraph' ? 'selected' : '';
            $ftImage = $fieldtype === 'image' ? 'selected' : '';
            $ftLogo = $fieldtype === 'logo' ? 'selected' : '';
            $ftRevLogo = $fieldtype === 'revLogo' ? 'selected' : '';

            $fields = "<option value=''>Select Custom Field</option>";
            $fields .= "<option value='Add Stamps'>Add Stamps</option>";

            foreach ($cf as $cfItem) {
                if (!is_object($cfItem) || !isset($cfItem->name)) continue;
                $cfSelected = (trim($cfItem->name) === trim($customField)) ? 'selected' : '';
                $fields .= '<option value="' . e($cfItem->name) . '" ' . $cfSelected . '>' . e($cfItem->name) . '</option>';
            }
        @endphp

        <tr style="{{ $color }}">
            <td>
                <input type="checkbox" value="{{ $cvValue->id }}" name="cv[{{ $i }}][select]" id="select_{{ $i }}" {{ $selected }}>
                <input type="hidden" name="cv[{{ $i }}][cv_id]" value="{{ $cvId }}">
            </td>
            <td style="max-width:300px;">
                <strong>{{ $cvValue->name }}</strong><br />
                <span class="small">{{ $cvValue->fieldKey }}</span>
                <input type="hidden" name="cv[{{ $i }}][name]" value="{{ $cvValue->name }}">
                <input type="hidden" name="cv[{{ $i }}][fieldKey]" value="{{ $cvValue->fieldKey }}">
            </td>
            <td colspan="2">
                <div style="margin-top:10px;">
                    <label for="customField_{{ $i }}">Custom Field Form Data
                        <i class="bi bi-question-circle-fill" data-toggle="tooltip" title="This is the form field from your location that will update the custom value when updating via webhook."></i>
                    </label>
                    <select style="margin-left:15px;" name="cv[{{ $i }}][customField]" id="customField_{{ $i }}"
                        data-index="{{ $i }}"
                        onchange="autoCheck(this);"
                        onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();">
                        {!! $fields !!}
                    </select>
                </div>
            </td>
            <td>
                <a class="btn btn-primary" onclick="showAdv('{{ $cvValue->id }}');">Advanced</a>
            </td>
        </tr>

        {{-- Advanced Row --}}
        <tr style="{{ $color }} border-top:1px dashed #f9f9f9; display:none;" class="adv_{{ $cvValue->id }}">
            <td>&nbsp;</td>
            <td style="padding-top:25px;">
                <div class="form-group">
                    <label for="tooltip_{{ $i }}">Tool Tip</label>
                    <textarea rows="5" style="width:100%;" name="cv[{{ $i }}][tooltip]" id="tooltip_{{ $i }}"
                        data-index="{{ $i }}"
                        onchange="autoCheck(this);"
                        onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();">{{ $tooltip }}</textarea>
                </div>
                <div class="form-group">
                    <label for="defaultv_{{ $i }}">Default Value</label>
                    <textarea rows="5" style="width:100%;" name="cv[{{ $i }}][defaultv]" id="defaultv_{{ $i }}"
                        data-index="{{ $i }}"
                        onchange="autoCheck(this);"
                        onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();">{{ $default }}</textarea>
                </div>
            </td>
            <td style="text-align:center; padding-top:25px;">
                <div>
                    <label for="fieldtype_{{ $i }}">Field Type
                        <i class="bi bi-question-circle-fill" data-toggle="tooltip" title="Field type allows you to set the data type of the field."></i>
                    </label>
                    <select style="width:100%;" name="cv[{{ $i }}][fieldType]" id="fieldType_{{ $i }}"
                        data-index="{{ $i }}"
                        onchange="autoCheck(this);"
                        onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();">
                        <option value="text" {{ $ftText }}>Text</option>
                        <option value="boolean" {{ $ftBoolean }}>YES/NO</option>
                        <option value="paragraph" {{ $ftPar }}>Paragraph</option>
                        <option value="image" {{ $ftImage }}>Image</option>
                        <option value="logo" {{ $ftLogo }}>Logo</option>
                        @if (session('role') === 'Admin')
                        <option value="revLogo" {{ $ftRevLogo }}>Review Logo</option>
                        @endif
                    </select><br />
                    <label for="readOnly_{{ $i }}">Read Only</label>
                    <input type="checkbox" style="margin:15px 0 0 15px;" value="yes" name="cv[{{ $i }}][readonly]" {{ $readonly }}><br />
                    <label for="wysiwyg_{{ $i }}">WYSIWYG</label>
                    <input type="checkbox" style="margin:5px 0 0 15px;" value="yes" name="cv[{{ $i }}][wysiwyg]" {{ $wysiwyg }}>
                    <br /><span class="small">WYSIWYG on Paragraphs Only</span>
                    <hr style="margin:5px auto;">
                    <label for="sort_order_{{ $i }}">Display Order</label>
                    <input type="text" name="cv[{{ $i }}][sort_order]" size="4" value="{{ $sort }}"
                        data-index="{{ $i }}"
                        onchange="autoCheck(this);"
                        onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();"/>
                    <br /><small>Used when using the Power Tools Custom Values Updater Form</small>
                </div>
            </td>
            <td colspan="2" style="padding-top:25px;">
                <label for="resource_{{ $i }}">Resources
                    <i class="bi bi-question-circle-fill" data-toggle="tooltip" title="Provide your end user with additional resource links, templates, or downloads"></i>
                </label>
                <textarea style="width:100%;" rows="5" name="cv[{{ $i }}][resource]" id="resource_{{ $i }}"
                    data-index="{{ $i }}"
                    onchange="autoCheck(this);"
                    onkeyup="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();">{{ $resource }}</textarea>
            </td>
        </tr>
        @endforeach

        <input type="hidden" name="totalCV" value="{{ $i }}">
    </tbody>
</table>
