<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Departmentuser;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class RightsController extends Controller
{

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * Show all Users in the current Pagination
     */
    public function index($pagination)
    {
        $users = User::skip( 8 * $pagination)->take(8)->get();

        foreach($users as $user){
            $departmentNames = array();
            $departments = Departmentuser::where('user_id',$user->id)->get();
            foreach($departments as $department){
                $departmentName = Department::where('id',$department->department_id)->get()->first()->departmentname;
                $strippedDepartmentName = str_replace(' ', '', $departmentName);
                array_push($departmentNames, $strippedDepartmentName);
            }
            $user->departments = $departmentNames;
        }

        return view('Users.Rights.index',['pagination' => $pagination,'users' => $users]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * Show the User with the specified id
     */
    public function show($id)
    {
        return view('Users.User.index');
    }

    public function create(Request $request)
    {
        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->password = $request->password;
        $user->passwordverify = $request->passwordverify;

        // $user->save();

        return view('Users.Rights.create');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * Gets User with specified id and passes it to View to edit the Users propertys
     */
    public function edit($id)
    {
        $currentUser = Admin::where(['admin-token' => session()->get('_token'), 'id' => session()->get('id')])->first() ??
            User::where(['user-token' => session()->get('_token'), 'id' => session()->get('id')])->first();

        // if its not a admin or not the user with the id to look for then abort further actions
        if(!$currentUser instanceof Admin)
        {
            if(strval($currentUser->id) !== $id ){
                abort(404);
            }
        }



        $user = User::where('id',$id)->first();
        $departments = Departmentuser::where(['user_id' => $user->id])->get();

        $possibleDepartments = [
            'Finance',
            'Controlling',
            'Development',
            'Marketing',
            'Human Resources',
        ];
        $notSelectedDepartments = [];

        for($x = 1; $x <= 5; $x++)
        {
            if(!$departments->contains('department_id',$x))
            {
                $notSelectedDepartments[] = $x;
            }
        }


        return view('Users.Rights.edit',
            [
                'user' => $user,
                'departmentusers' => $departments,
                'possibleDepartments' => $possibleDepartments,
                'notSelectedDepartments' => $notSelectedDepartments,
            ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * Gets User with specified id and passes it to View to edit the Users propertys
     */
    public function editNoDepartment($id)
    {
        $currentUser = Admin::where(['admin-token' => session()->get('_token'), 'id' => $id])->first() ??
            User::where(['user-token' => session()->get('_token'), 'id' => $id])->first();

        // if its not a admin or not the user with the id to look for then abort further actions
        if(!$currentUser instanceof Admin)
        {
            if(strval($currentUser->id) !== $id ){
                abort(404);
            }
        }



        $user = User::where('id',$id)->first();
        $departments = Departmentuser::where(['user_id' => $user->id])->get();

        $possibleDepartments = [
            'Finance',
            'Controlling',
            'Development',
            'Marketing',
            'Human Resources',
        ];
        $notSelectedDepartments = [];

        for($x = 1; $x <= 5; $x++)
        {
            if(!$departments->contains('department_id',$x))
            {
                $notSelectedDepartments[] = $x;
            }
        }


        return view('Users.Rights.editNoDepartment',
            [
                'user' => $user,
                'departmentusers' => $departments,
                'possibleDepartments' => $possibleDepartments,
                'notSelectedDepartments' => $notSelectedDepartments,
            ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * Updates the User after View from edit method has been submitted
     */
    public function update(Request $request,$id)
    {

        // check which departments has been sent and if its one of the 5 departments
        for($x = 0; $x <= 4; $x++)
        {
            if($request["department".$x] === null)  continue;
            // if its not one of the departments redirect back
            if(!($request["department".$x] === 'Finance' ||
                $request["department".$x] === 'Controlling' ||
                $request["department".$x] === 'Development' ||
                $request["department".$x] === 'Marketing' ||
                $request["department".$x] === 'Human Resources'))
            {
                return redirect()->back()
                    ->withErrors(['msg', Lang::get('validation.department_failed')]);
            }
        }

        $request->validate([
            'name' => 'required|alpha|max:200',
            'lastname' => 'required|alpha|max:200',
        ]);


        $user = User::where('id',$id)->first();

        if($user->email !== $request->email){
            $request->validate(['email' => 'required|email:rfc|unique:user|unique:admin']);
            $user->email = $request->email;
        }

        $user->name = $request->name;
        $user->lastname = $request->lastname;

        // Profile image upload
        if($request->hasFile('profileImage'))
        {
            $user->picture = $request->file('profileImage')->hashName();
            $request->file('profileImage')->store('public');
        }

        $user->save();

        $departments = [
            'Finance' => 1,
            'Controlling' => 2,
            'Development' => 3,
            'Marketing' => 4,
            'Human Resources' => 5,
        ];

        for($x = 0; $x <= 4;$x++ )
        {
            // If department has been sent to request so it can be added
            if(isset($request["department{$x}"]))
            {
                $departmentName = $request["department{$x}"];

                $departmentUser = Departmentuser::where(
                    [
                        'department_id' => $departments[$departmentName],
                        'user_id' => $id
                    ])->get();


                // does user exist already for this department?
                if($departmentUser->count() > 0)
                {
                    unset($departments[$this->determineDepartmentType($x)]);
                    continue;
                }
                // no departmentuser, so add a new departmentuser
                else{
                    $newDepartmentUser = new Departmentuser();
                    $newDepartmentUser->user_id = $id;
                    $newDepartmentUser->department_id = $departments[$departmentName];
                    $newDepartmentUser->save();

                    unset($departments[$this->determineDepartmentType($x)]);
                }
            }
        }

        // the left departments,which havent been added to request  will be queried here
        // so the departmentuser of the left departments will be deleted
        foreach($departments as $val)
        {
            $leftUser = Departmentuser::where(['department_id' => $val,'user_id' => $id])->get();
            if($leftUser->count() > 0)
                $leftUser->first()->delete();
        }

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * Updates the User after View from edit method has been submitted
     */
    public function updateNoDepartment(Request $request,$id)
    {


        $request->validate([
            'name' => 'required|alpha|max:200',
            'lastname' => 'required|alpha|max:200',
        ]);


        $user = User::where('id', $id)->first();
        if($user->email !== $request->email){
            $request->validate(['email' => 'required|email:rfc|unique:user|unique:admin']);
            $user->email = $request->email;
        }

        $user->name = $request->name;
        $user->lastname = $request->lastname;

        // Profile image upload
        if($request->hasFile('profileImage'))
        {
            $user->picture = $request->file('profileImage')->hashName();
            $request->file('profileImage')->store('public');
        }

        $user->save();


        return redirect()->route('user.index',['id' => request()->session()->get('id') ]);
    }

    private function determineDepartmentType($val)
    {
        if($val == 0) return "Finance";
        if($val == 1) return "Controlling";
        if($val == 2) return "Development";
        if($val == 3) return "Marketing";
        if($val == 4) return "Human Resources";
    }
}
