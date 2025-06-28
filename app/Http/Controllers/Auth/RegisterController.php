<?php

namespace App\Http\Controllers\Auth;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
    $visitadoras = User::where('role_id', 3)->get();
    // Verificar rol
    if (!in_array(auth()->user()->role->name, ['Admin', 'Jefe Proyecto'])) {
        abort(403, 'Acceso no autorizado');
    }
        // Obtener todos los roles de la base de datos
        $roles = Role::all();
        return view('auth.register', compact('roles', 'visitadoras'));  
    }

    /**
     * Registrar un nuevo usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
            public function register(Request $request)
    {
        if ($request->role_id != 4) {
            $request->merge(['visitadora_id' => null]);
        }
        $this->validator($request->all())->validate();

        // Obtener el rol por ID
        $role = Role::findOrFail($request->role_id);

        // Crear el usuario con el rol asignado
        $user = $this->create($request->all());

        // Si el rol del usuario es 4 (Doctor), crear un cliente asociado y asignar la visitadora
        if ($user->role_id == 4) {
            Cliente::create([
                'nombre' => $user->name,
                'cmp' => $request->cmp,
                'tipo_delivery' => $request->tipo_delivery,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'user_id' => $user->id,
                'visitadora_id' => $request->visitadora_id, 
            ]);
        }

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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'cmp' => ['nullable', 'string', 'unique:clientes,cmp', 'max:10'],
            'tipo_delivery' => ['nullable', 'in:Recojo en tienda,Entrega a domicilio'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ];

        // Solo agregar esta validación si el rol es Doctor (4)
        if (isset($data['role_id']) && $data['role_id'] == 4) {
            $rules['visitadora_id'] = ['required', 'exists:users,id'];
        }

        return Validator::make($data, $rules, [
            'email.unique' => 'El correo electrónico ya está registrado.',
            'cmp.unique' => 'El CMP ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'visitadora_id.required' => 'Debe seleccionar una visitadora médica para el doctor.',
            'visitadora_id.exists' => 'La visitadora médica seleccionada no es válida.',
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
            'last_name' => $data['last_name'] ?? null,
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
        $roles = Role::all();  
        $visitadoras = User::where('role_id', 3)->get();
        return view('registrar.edit', compact('user', 'roles', 'visitadoras'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $cliente = Cliente::where('user_id', $user->id)->first();
        $clienteId = optional($cliente)->id;

        // Verificar si tiene formulaciones o cotizaciones asociadas
        $tieneAsociaciones = $cliente && (
            $cliente->formulaciones()->exists() ||
            $cliente->cotizaciones()->exists()
        );

        // Si es doctor y quiere cambiar de rol, pero tiene relaciones, bloquear
        if ($user->role_id == 4 && $request->role_id != 4 && $tieneAsociaciones) {
            return redirect()->back()->with('error', 'No se puede cambiar el rol porque este doctor tiene formulaciones o cotizaciones asociadas.');
        }

        // Si no es doctor, limpiar visitadora
        if ($request->role_id != 4) {
            $request->merge(['visitadora_id' => null]);
        }

        // Validación de datos
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
            'cmp' => [
                'nullable',
                'string',
                'max:10',
                Rule::unique('clientes', 'cmp')->ignore($clienteId),
            ],
            'visitadora_id' => ['nullable', 'required_if:role_id,4', 'exists:users,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'El correo electrónico ya está registrado. Por favor, use otro.',
            'cmp.unique' => 'El CMP ya está registrado. Por favor, use otro.',
            'visitadora_id.required_if' => 'Debe seleccionar una visitadora médica para el doctor.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        // Detectar cambio de rol
        $roleChanged = $user->role_id !== $request->role_id;

        // Si cambió a doctor
        if ($roleChanged && $request->role_id == 4) {
            if (!$cliente) {
                Cliente::create([
                    'nombre' => $request->name . ' ' . ($request->last_name ?? ''),
                    'cmp' => $request->cmp,
                    'user_id' => $user->id,
                    'visitadora_id' => $request->visitadora_id,
                ]);
            } else {
                $cliente->update([
                    'nombre' => $request->name . ' ' . ($request->last_name ?? ''),
                    'cmp' => $request->cmp,
                    'visitadora_id' => $request->visitadora_id,
                ]);
            }

        } elseif ($roleChanged && $request->role_id != 4) {
            // Si dejó de ser doctor
            Cliente::where('user_id', $user->id)->delete();

        } else {
            // Si sigue siendo doctor
            if ($user->role_id == 4 && $cliente) {
                $cliente->update([
                    'nombre' => $request->name . ' ' . ($request->last_name ?? ''),
                    'cmp' => $request->cmp,
                    'visitadora_id' => $request->visitadora_id,
                ]);
            }
        }
        $data = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        // Si hay contraseña, se encripta y se agrega
        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        // Actualizar usuario
        $user->update($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente');
    }

        // Eliminar un usuario
        public function destroy($id)
    {
        $user = User::findOrFail($id);
        $cliente = $user->cliente;
        $tieneAsociaciones = $cliente && (
            $cliente->formulaciones()->exists() || 
            $cliente->cotizaciones()->exists()
        );
        // Si tiene relaciones, no permitir la eliminación
        if ($tieneAsociaciones) {
            return redirect()->back()->with('error', 'No se puede eliminar este usuario porque tiene formulaciones o cotizaciones asociadas.');
        }
        // Si no tiene relaciones, eliminar normalmente
        $user->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado exitosamente');
    }
}
