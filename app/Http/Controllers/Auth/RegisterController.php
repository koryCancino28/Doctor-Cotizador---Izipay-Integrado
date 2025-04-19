<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | Register Controller
    |----------------------------------------------------------------------
    |
    | Este controlador maneja el registro de nuevos usuarios así como su
    | validación y creación, sin iniciar sesión automáticamente.
    |
    */

    /**
     * Donde redirigir a los usuarios después de registrarse.
     *
     * @var string
     */
    protected $redirectTo = '/usuarios';


    /**
     * Mostrar el formulario de registro.
     */
    public function showRegistrationForm()
    {
        if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    // Verificar rol
    if (!in_array(auth()->user()->role->name, ['Admin', 'Jefe Proyecto'])) {
        abort(403, 'Acceso no autorizado');
    }
        // Obtener todos los roles de la base de datos
        $roles = Role::all();
        return view('auth.register', compact('roles'));  
    }

    /**
     * Registrar un nuevo usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // dd($request->all());
        // Validar los datos del formulario de registro
        $this->validator($request->all())->validate();

         // Obtener el rol por ID
         $role = Role::findOrFail($request->role_id);
        // Crear el usuario con el rol asignado
        $user = $this->create($request->all()); 

        // Redirigir a la página de usuarios sin loguearse
        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado exitosamente');
    }

    /**
     * Obtener un validador para una solicitud de registro entrante.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'], // Asegúrate que 'email' se valide en la tabla users
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
        ], [
            'email.unique' => 'El correo electrónico ya está registrado. Por favor, use otro.', // Mensaje personalizado
        ]);
    }

    /**
     * Crear una nueva instancia de usuario después de una validación exitosa.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],   // Asignar el role_id al usuario
        ]);
    }

    // Mostrar todos los usuarios registrados
    public function index()
    {
        $users = User::with('role')->get();
        $users = User::all(); // Obtener todos los usuarios registrados
        return view('registrar.registrar', compact('users'));
    }

    // Editar un usuario
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();  // Obtener todos los roles disponibles
        return view('registrar.edit', compact('user', 'roles'));
    }

    // Actualizar un usuario
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id', // Validar que el role_id exista en la tabla roles
        ]);
    
        $user = User::findOrFail($id); // Buscar el usuario por su ID
        // Actualizar el usuario incluyendo el role_id
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id, // Asegúrate de actualizar el role_id
        ]);
    
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente');
    }

    // Eliminar un usuario
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado exitosamente');
    }
}
