<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
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
        return view('auth.register');  
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

        // Crear el usuario sin iniciar sesión automáticamente
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
        ]);
    }

    // Mostrar todos los usuarios registrados
    public function index()
    {
        $users = User::all(); // Obtener todos los usuarios registrados
        return view('registrar.registrar', compact('users'));
    }

    // Editar un usuario
    public function edit($id)
    {
        $user = User::findOrFail($id); // Buscar el usuario por su ID
        return view('registrar.edit', compact('user'));
    }

    // Actualizar un usuario
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        ]);

        $user = User::findOrFail($id); // Buscar el usuario por su ID
        $user->update($request->only('name', 'email'));

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
