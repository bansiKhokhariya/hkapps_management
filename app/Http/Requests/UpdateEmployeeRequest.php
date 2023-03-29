<?php

namespace App\Http\Requests;

use App\Events\UserEvent;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(User $employee)
    {

        return [
            'name' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'designation' => 'required',
        ];
    }

    public function persist(User $employee)
    {
        $id = Auth::user()->id;
        $auth_user = User::find($id);


        $convertOldRole = str_replace(' ', '_', strtolower($employee->name));
        $getRole = Role::where('name', $convertOldRole)->first();
        $convertRole = str_replace(' ', '_', strtolower($this->name));
        $updateRole = Role::find($getRole->id);
        $updateRole->name = $convertRole;
        $updateRole->save();
        $role = Role::findByName($convertRole, 'web');

        $employee->fill($this->validated());


        if($this->password){
            $employee->password = bcrypt($this->password);
        }else{
            $employee->password = bcrypt($employee->password);
        }

        if ($this->hasFile('profile_image')) {
            $file = $this->file('profile_image');
            $file_name = $file->getClientOriginalName();
            $file->move(public_path('/profile_image'), $file_name);
            $file_path = URL::to('/') . '/profile_image/' . $file_name;
        } else {
            $file_path = null;
        }
        if ($this->profile_image == null) {
            $employee->profile_image = $employee->profile_image;
        } else {
            $employee->profile_image = $file_path;
        }
        $employee->company_master_id = $this->company_master_id;
        $employee->email = $this->email;
        $employee->roles = $convertRole;
        $employee->assignRole($role);
        $employee->save();

        // call event
        // event(new UserEvent($auth_user));

        return $employee;
    }
}
