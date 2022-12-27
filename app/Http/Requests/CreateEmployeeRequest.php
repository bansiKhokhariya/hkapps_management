<?php

namespace App\Http\Requests;

use App\Events\UserEvent;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Role;


class CreateEmployeeRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'roles' => 'required',
            'designation'=>'required'
        ];
    }
    public function persist()
    {
        $id = Auth::user()->id;
        $auth_user = User::find($id);

        $role = Role::findByName($this->roles, 'web');
        $employee = new User($this->validated());
        $employee->password = bcrypt($this->password);

        if ($this->hasFile('profile_image')) {
            $file = $this->file('profile_image');
            $file_name = $file->getClientOriginalName();
            $file->move(public_path('/profile_image'), $file_name);
            $file_path =  URL::to('/') . '/profile_image/'.$file_name;
        } else {
            $file_path = null;
        }
        $employee->profile_image = $file_path;


        $employee->assignRole($role);
        $employee->save();

        // CALL EVENT
        // event(new UserEvent($auth_user));

        return $employee;
    }
}
