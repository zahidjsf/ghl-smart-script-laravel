<?php

namespace App\Http\Controllers\FrontPanel;

use App\Http\Controllers\Controller;
use App\Models\CollectionAssign;
use App\Models\CustomValue;
use App\Models\CustomValueCollection;
use App\Models\Location;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomValueController extends Controller
{
    public function cvUpdater()
    {
        return view('frontpanel.cvupdateer.index');
    }

    public function getCollections()
    {
        $authUser = auth()->user();
        $user_id = $authUser->id;

        $locations = CustomValueCollection::where(['a_id' => $user_id]);
        return DataTables::of($locations)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                $html = '<a href="' . route('frontend.smart_reward.editcollection', ['id' => $row->id]) . '" class="btn btn-sm btn-primary">Edit Collection</a> ';
                $html .= '<a style="margin-right:4px" href="#" data-url="' . route('frontend.smart_reward.copycollection', ['id' => $row->id]) . '" class="btn btn-sm btn-success duplicate-collection">Copy</a>';
                if ($row->locked !== 'yes') {
                    $html .= '<a style="margin-right:4px" href="#" data-url="' . route('frontend.smart_reward.removecollection', ['id' => $row->id]) . '" class="btn btn-sm btn-danger remove-collection">Remove</a>';
                }

                return $html;
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    public function addcollection()
    {
        $authUser = auth()->user();
        $a_id = $authUser->id;
        $agencyLocations = Location::where('a_id', $a_id)->get();
        return view('frontpanel.cvupdateer.add-collection', get_defined_vars());
    }

    public function createCollection(Request $request)
    {
        $request->validate([
            // 'id' => 'required',
            'col_id' => 'required',
            'cf_loc' => 'nullable',
        ]);

        $location = DB::table('locations')->where('id', $request->input('id'))->first();

        if (empty($location) || empty($location->loc_id)) {
            return response('', 204); // or abort(404)
        }

        $oauth = checkOauthLocation($location->loc_id);
        $cv = getCustomValues(decryptAPI($location->apikey), $location->loc_id);
        // $cvDB = getCustomValuesByColId($request->input('col_id'));

        if (isset($cv['error'])) {
            return response($cv['error'], 500);
        }

        if ($request->input('cf_loc') == $location->loc_id || empty($request->input('cf_loc')) || $request->input('cf_loc') == "Same Location As Custom Values Above") {
            $cf = getCustomFields(decryptAPI($location->apikey), $location->loc_id);
        } else {

            
            $newLoc = Location::where('loc_id', $request->input('cf_loc'))->first();

            $url = 'locations/'.$locid.'/customFields?model=contact';
            $cf = CRM::crmV2(auth()->user()->id, $url,  'get', '', [], false, $locId);

            $url = 'locations/' . $locId . '/customValues';
            $response = CRM::crmV2(auth()->user()->id, $url,  'get', '', [], false, $locId);

            // $newLoc = getSingleLocationGHLID($request->input('cf_loc'));
            $cf = getCustomFields(decryptAPI($newLoc['apikey']), $newLoc['loc_id']);
        }

        // Now build the HTML (this part can also be moved to a Blade view if preferred)
        $html = '<table class="table table-hover cvTableImport"><thead><tr>
                <th>Add</th> <th>Name</th>
                <th>Custom Field To Map <i class="bi bi-question-circle-fill" data-toggle="tooltip" title="Select your custom field, to map to your custom value."></i></th>
                <th></th><th></th></tr></thead><tbody>';

        $i = 0;
        $c = 0;

        foreach ($cv['customValues'] as $v) {
            $color = ($c == 0) ? "background:#e1e1e1;" : "background:#fff;";
            $c = 1 - $c;
            $i++;

            // Set defaults
            $selected = $tooltip = $fieldtype = $resource = $readonly = $wysiwyg = $customField = $sort = $default = '';
            $ftText = $ftBoolean = $ftPar = $ftImage = $ftLogo = $ftRevLogo = '';

            // Match with DB values
            // foreach ($cvDB as $cvRow) {
            //     if (trim(str_replace('"', '', $cvRow['name'])) == trim(str_replace('"', '', $v['name']))) {
            //         $selected = "checked";
            //         $tooltip = $cvRow['tooltip'];
            //         $fieldtype = $cvRow['fieldType'];
            //         $resource = $cvRow['resources'];
            //         $readonly = $cvRow['cvaction'] == "readonly" ? "checked" : "";
            //         $wysiwyg = $cvRow['cvattribute'] == "wysiwyg" ? "checked" : "";
            //         $customField = $cvRow['custom_field'];
            //         $sort = $cvRow['cv_order'];
            //         $default = $cvRow['defaultv'] ?? '';

            //         switch ($fieldtype) {
            //             case "text":
            //                 $ftText = "selected";
            //                 break;
            //             case "boolean":
            //                 $ftBoolean = "selected";
            //                 break;
            //             case "paragraph":
            //                 $ftPar = "selected";
            //                 break;
            //             case "image":
            //                 $ftImage = "selected";
            //                 break;
            //             case "logo":
            //                 $ftLogo = "selected";
            //                 break;
            //             case "revLogo":
            //                 $ftRevLogo = "selected";
            //                 break;
            //         }
            //     }
            // }

            // Generate custom field dropdown options
            $fields = '';
            foreach ($cf as $cfGroup) {
                if (!is_array($cfGroup)) continue;
                usort($cfGroup, "cmp");

                foreach ($cfGroup as $cfv) {
                    $cfselected = (trim(str_replace('"', '', $cfv['name'])) == trim(str_replace('"', '', $customField))) ? "selected" : "";
                    $fields .= "<option value='" . str_replace('"', '', $cfv['name']) . "' $cfselected>" . str_replace('"', '', $cfv['name']) . "</option>";
                }
            }

            $html .= '<tr style="' . $color . '">';
            $html .= '<td><input type="checkbox" value="' . $v['id'] . '" name="select_' . $i . '" id="select_' . $i . '" ' . $selected . '></td>';
            $html .= '<td style="max-width:300px;"><strong>' . str_replace('"', '', $v['name']) . '</strong><br/><span class="small">' . $v['fieldKey'] . '</span>
                  <input type="hidden" name="name_' . $i . '" value="' . str_replace('"', '', $v['name']) . '">
                  <input type="hidden" name="fieldKey_' . $i . '" value="' . $v['fieldKey'] . '"></td>';
            $html .= '<td colspan="2"><label for="customField_' . $i . '">Custom Field Form Data</label>
                  <select name="customField_' . $i . '" id="customField_' . $i . '" onchange="selectRow(this);" oninput="this.onchange();">' . $fields . '</select></td>';
            $html .= '<td><a class="btn btn-primary" onclick="showAdv(\'' . $v['id'] . '\');">Advanced</a></td>';
            $html .= '</tr>';

            $html .= '<tr style="' . $color . ' border-top:1px dashed #f9f9f9; display:none;" class="adv_' . $v['id'] . '">';
            $html .= '<td></td>';
            $html .= '<td style="padding-top:25px;"><label>Tool Tip</label><textarea name="tooltip_' . $i . '">' . $tooltip . '</textarea>
                  <label>Default Value</label><textarea name="defaultv_' . $i . '">' . $default . '</textarea></td>';
            $html .= '<td style="text-align:center; padding-top:25px;"><label>Field Type</label>
                    <select name="fieldType_' . $i . '">
                        <option value="text" ' . $ftText . '>Text</option>
                        <option value="boolean" ' . $ftBoolean . '>YES/NO</option>
                        <option value="paragraph" ' . $ftPar . '>Paragraph</option>
                        <option value="image" ' . $ftImage . '>Image</option>
                        <option value="logo" ' . $ftLogo . '>Logo</option>';

            if (session('role') === 'Admin') {
                $html .= '<option value="revLogo" ' . $ftRevLogo . '>Review Logo</option>';
            }

            $html .= '</select><br/>
                  <label>Read Only</label><input type="checkbox" name="readonly_' . $i . '" ' . $readonly . '>
                  <label>WYSIWYG</label><input type="checkbox" name="wysiwyg_' . $i . '" ' . $wysiwyg . '><br/>
                  <label>Display Order</label><input type="text" name="sort_order_' . $i . '" value="' . $sort . '"/></td>';

            $html .= '<td colspan="2"><label>Resources</label><textarea name="resource_' . $i . '" rows="5">' . $resource . '</textarea></td>';
            $html .= '</tr>';
        }

        $html .= '<input type="hidden" name="totalCV" value="' . $i . '">';
        $html .= '</tbody></table>';

        return response($html);
    }

    public function getCustomValue($id)
    {
        dd($id);
    }

    public function editCollection($id)
    {
        dd($id);
    }

    public function copyCollection($id)
    {
        $collection = CustomValueCollection::find($id);
        $view = view('frontpanel.cvupdateer.copy-collection', get_defined_vars())->render();
        return response()->json(['view' => $view]);
    }

    public function duplicateCollection(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'col' => 'required|exists:customvaluecollections,id',
        ]);
        try {

            DB::beginTransaction();
            $originalCollection = CustomValueCollection::findOrFail($request->col);
            $newCollection = $originalCollection->replicate();
            $newCollection->name = $request->name;
            $newCollection->description = $request->col_desc ?? $originalCollection->description;
            $newCollection->save();
            $originalValues = CustomValue::where('col_id', $originalCollection->id)->get();
            foreach ($originalValues as $value) {
                $newValue = $value->replicate();
                $newValue->col_id = $newCollection->id;
                $newValue->save();
            }

            $collectionAssign = new CollectionAssign();
            $collectionAssign->loc_id = $originalCollection->orig_loc_id;
            $collectionAssign->col_id = $newCollection->id;
            $collectionAssign->a_id = $originalCollection->a_id;
            $collectionAssign->proj_id = 2;
            $collectionAssign->save();

            DB::commit();
            return redirect()->back()->with('success', 'Collection duplicated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to duplicate collection: ' . $e->getMessage());
        }
    }

    public function removeCollection($collectionId)
    {
        try {
            DB::beginTransaction();
            $collection = CustomValueCollection::findOrFail($collectionId);
            if ($collection->locked === 'yes') {
                return response()->json(['status' => 'error', 'message' => 'This collection is locked and cannot be removed.']);
            }
            CustomValue::where('col_id', $collectionId)->delete();
            CollectionAssign::where('col_id', $collectionId)->delete();
            DB::table('collection_assign')->where('col_id', $collectionId)->delete();
            $collection->delete();
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Collection removed successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to remove collection: ' . $e->getMessage());
        }
    }
}
