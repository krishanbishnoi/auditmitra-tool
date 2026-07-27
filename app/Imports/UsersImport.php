<?php

namespace App\Imports;

use App\User;
use App\Model\Role;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Mail;
use Auth;
class UsersImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * 
     * @param Collection $rows
     */


     private $errors = [];
 
     private $data; 
 
 
 
     public function __construct(array $data = [])
 
     {
 
         $this->roles = $data; 
         $this->authUser = $data['authUser'] ?? null;
         $this->client_id = $data['client_id'] ?? null;
 
     }

    public function collection(Collection $rows)
    {
        ini_set("memory_limit", "-1");

        ini_set("max_execution_time", 5000);
        
        foreach ($rows as $row) {
            // Find role ID based on the role name
            $role = Role::where('name', $row['role'])->first();

            // Check if the user already exists
            $user = User::where('email', $row['email'])->first();

            $userData = [
                'employee_id' => $row['employeeid'],
                'name' => $row['fullname'],
                'email' => $row['email'],
                'team' => $row['team'],
                'user_role_id' => $role ? $role->id : null,
                'mobile' => $row['mobile'],
                'audit_agency' =>  'Qdegrees',
                'password' => bcrypt('Admin@123'),
                'created_by' => $this->authUser ? $this->authUser->email : null,
                'audit_agency_id' => $this->authUser && $this->authUser->hasRole('Super Admin')
                ? $this->client_id  // or you can pass some specific audit_agency_id from form if needed
                : ($this->authUser ? $this->authUser->id : null),
                
                'client_id' => $this->authUser && $this->authUser->hasRole('Super Admin')
                ? $this->client_id
                : ($this->authUser ? $this->authUser->client_id : null),
                
            ];

            if ($user) {
                // Update existing user
                $user->update($userData);
            } else {
                // Create new user
                $user = User::create($userData);
            }
            $user->assignRole($row['role']);
            // Send email
          //  $this->sendEmail($user, $row['password']);
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            // '*.employeeid' => 'required|unique:users,employee_id',
            '*.fullname' => 'required',
         //   '*.team' => 'required',
            '*.email' => 'required|unique:users,email',
            '*.role' => 'required|exists:roles,name',
         //   '*.mobilenumber' => 'required',
          //  '*.auditagency' => 'required',
          //  '*.password' => 'required|string|min:6',
        ];
    }

    /**
     * Send email to user with login details.
     *
     * @param User $user
     * @param string $password
     */
    protected function sendEmail(User $user, string $password)
    {
        $url = url('login');

        Mail::send('emails.createUser', ['user' => $user, 'password' => $password, 'url' => $url], function ($m) use ($user) {
            $m->to($user->email, $user->name)->subject('Welcome Audit');
        });
    }
}
