<?php

namespace App\Http\Requests;

use App\Models\AllConsole;
use Illuminate\Foundation\Http\FormRequest;

class CreateAllConsoleRequest extends FormRequest
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
//            'email' => 'required|email',
        ];
    }
    public function persist()
    {
        $allConsole = new AllConsole($this->validated());
        $allConsole->manageBy_id = $this->manageBy_id;
        $allConsole->email = $this->email;
        $allConsole->password = $this->password;
        $allConsole->consoleName = $this->consoleName;
        $allConsole->status = $this->status;
        $allConsole->mobile = $this->mobile;
        $allConsole->device = $this->device;
        $allConsole->remarks = $this->remarks;
        $allConsole->blogger = $this->blogger;
        $allConsole->privacy = $this->privacy;
        $allConsole->save();

        // call event
        // event(new RedisDataEvent());

        return $allConsole;
    }
}
