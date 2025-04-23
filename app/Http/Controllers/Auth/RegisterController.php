<?php

namespace App\Http\Controllers\Auth;
use App\Models\Cliente;
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
    // Validar los datos del formulario de registro
    $this->validator($request->all())->validate();

    // Obtener el rol por ID
    $role = Role::findOrFail($request->role_id);

    // Crear el usuario con el rol asignado
    $user = $this->create($request->all());

    // Si el rol del usuario es 4 (por ejemplo, "Doctor"), crear un registro en la tabla clientes
    if ($user->role_id == 4) {
        // Crear el cliente asociado al usuario
        Cliente::create([
            'nombre' => $user->name,  
            'cmp' => $user->cmp,  
            'tipo_delivery' => $request->tipo_delivery,  
            'user_id' => $user->id, // Relacionamos el cliente con el usuario
        ]);
    }

    // Redirigir a la página de usuarios
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
            'cmp' => ['nullable', 'string', 'unique:users,cmp', 'max:10'], 
        ], [
            'email.unique' => 'El correo electrónico ya está registrado. Por favor, use otro.', // Mensaje personalizado
            'cmp.unique' => 'El CMP ya está registrado. Por favor, use otro.',
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
            'cmp' => $data['cmp'], // Guardar cmp si es doctor
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
        // Validar los datos de entrada
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,  // Excluir el correo del usuario actual
            'role_id' => 'required|exists:roles,id',
            'cmp' => 'nullable|string|max:10|unique:users,cmp,' . $id,  // Validar CMP como único, excepto el usuario actual
        ]);
    
        // Buscar el usuario por su ID
        $user = User::findOrFail($id);
    
        // Verificar si el rol ha cambiado
        $roleChanged = $user->role_id !== $request->role_id;
    
        if ($roleChanged) {
            if ($request->role_id == 4) {  // Si el rol cambia a 'Doctor' (suponemos que el rol Doctor tiene ID 4)
                // Verificar si el usuario ya tiene un cliente asociado
                $cliente = Cliente::where('user_id', $user->id)->first();
                if (!$cliente) {
                    // Si no existe, crear un nuevo cliente
                    Cliente::create([
                        'nombre' => $user->name,
                        'cmp' => $request->cmp,  // Asegúrate de pasar correctamente el CMP
                        'tipo_delivery' => $request->tipo_delivery,  
                        'user_id' => $user->id,
                    ]);
                } else {
                    // Si ya existe un cliente, actualizar el CMP
                    $cliente->update([
                        'cmp' => $request->cmp,  // Actualizar CMP si es necesario
                    ]);
                }
                // Asegúrate de que CMP esté presente al cambiar a Doctor
                $user->cmp = $request->cmp;
            } else {
                // Si el nuevo rol no es 'Doctor', eliminar el cliente asociado y el CMP
                Cliente::where('user_id', $user->id)->delete();
                $user->cmp = null;  // Eliminar CMP del usuario si ya no es Doctor
            }
        } else {
            // Si no ha cambiado el rol, mantener el CMP si sigue siendo Doctor
            if ($user->role_id == 4) {
                $user->cmp = $request->cmp;
            }
        }
    
        // Actualizar el usuario con los nuevos datos
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,  
            'cmp' => $user->cmp,  // Asegurarse de que CMP se actualiza correctamente
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
