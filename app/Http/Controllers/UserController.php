<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();

        $validacao = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validacao->fails()) {
            return $validacao->errors();
        };

        $defaultAvatar = "/users/avatar.png";

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'avatar' => $defaultAvatar
        ]);
        $user->token = $user->createToken($user->email)->accessToken;
        $user->avatar = asset($defaultAvatar);

        return ['status' => true, 'user' => $user];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
       // var_dump($request);

        $user = $request->user();
        $data = $request->all();

        if (isset($data['password'])) {
            $validacao = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => 'required|string|min:6|confirmed',
            ]);
            if ($validacao->fails()) {
                return ['status' => false, 'errors' => $validacao->errors()];;
            };
            $user->password = Hash::make($data['password']);
        } else {
            $validacao = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            ]);
            if ($validacao->fails()) {
                return ['status' => false, 'errors' => $validacao->errors()];;
            };
            //var_dump($data['avatar']);

            $user->name = $data['name'];
            $user->email = $data['email'];

        }

       // var_dump($data);
        if (isset($data['avatar']) && ($data['avatar'] != $user->avatar)) {
            Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
                $explode = explode(',', $value);
                $allow = ['png', 'jpg', 'svg', 'jpeg'];
                $format = str_replace(
                    [
                        'data:image/',
                        ';',
                        'base64',
                    ],
                    [
                        '', '', '',
                    ],
                    $explode[0]
                );
            // check file format
                if (!in_array($format, $allow)) {
                    return false;
                }
            // check base64 format
                if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                    return false;
                }
                return true;
            });
            $validacao = Validator::make($data, ['avatar' => 'base64image'], ['base64image' => 'Imagem inválida']);

            if ($validacao->fails()) {
                return ['status' => false, 'errors' => $validacao->errors()];
            }

            $time = time();
            $diretorioPai = 'users';
            $diretorioAvatar = $diretorioPai . DIRECTORY_SEPARATOR . 'profile_id' . $user->id;
            $ext = substr($data['avatar'], 11, strpos($data['avatar'], ';') - 11);
            $urlAvatar = $diretorioAvatar . DIRECTORY_SEPARATOR . $time . '.' . $ext;

            $file = str_replace('data:image/' . $ext . ';base64,', '', $data['avatar']);
            $file = base64_decode($file);
            if (!file_exists($diretorioPai)) {
                mkdir($diretorioPai, 0700);
            }
            if ($user->avatar) {
                if (file_exists($user->avatar)) {
                    unlink($user->avatar);
                }
            }
            if (!file_exists($diretorioAvatar)) {
                mkdir($diretorioAvatar, 0700);
            }

            file_put_contents($urlAvatar, $file);

            $user->avatar = $urlAvatar;
        }

        $user->save();

        $user->avatar = asset($user->avatar);

        $user->token = $user->createToken($user->email)->accessToken;

        return ['status' => true, 'user' => $user];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    /**
     * Realiza login no sistema.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $data = $request->all();
        $validacao = Validator::make($data, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:3',
        ]);

        if ($validacao->fails()) {
            return $validacao->errors();
        };

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = auth()->user();
            $user->token = $user->createToken($user->email)->accessToken;
            return ['status' => true, 'user' => $user];
        } else {
            return ['status' => false];
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function editar(Request $request)
    {
        $user = $request->user();
        $data = $request->all();

        if (isset($data['password'])) {
            $validacao = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => 'required|string|min:6|confirmed',
            ]);
            if ($validacao->fails()) {
                return ['status' => false, 'errors' => $validacao->errors()];;
            };
            $user->password = Hash::make($data['password']);
        } else {
            $validacao = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            ]);
            if ($validacao->fails()) {
                return ['status' => false, 'errors' => $validacao->errors()];;
            };
            $user->name = $data['name'];
            $user->email = $data['email'];

        }

        if (isset($data['avatar'])) {
            Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
                $explode = explode(',', $value);
                $allow = ['png', 'jpg', 'svg', 'jpeg'];
                $format = str_replace(
                    [
                        'data:image/',
                        ';',
                        'base64',
                    ],
                    [
                        '', '', '',
                    ],
                    $explode[0]
                );
            // check file format
                if (!in_array($format, $allow)) {
                    return false;
                }
            // check base64 format
                if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                    return false;
                }
                return true;
            });

            $validacao = Validator::make($data, ['avatar' => 'base64image'], ['base64image' => 'Imagem inválida']);

            if ($validacao->fails()) {
                return ['status' => false, 'errors' => $validacao->errors()];
            }
            $time = time();
            $diretorioPai = 'users';
            $diretorioAvatar = $diretorioPai . DIRECTORY_SEPARATOR . 'profile_id' . $user->id;
            $ext = substr($data['avatar'], 11, strpos($data['avatar'], ';') - 11);
            $urlAvatar = $diretorioAvatar . DIRECTORY_SEPARATOR . $time . '.' . $ext;

            $file = str_replace('data:image/' . $ext . ';base64,', '', $data['avatar']);
            $file = base64_decode($file);
            if (!file_exists($diretorioPai)) {
                mkdir($diretorioPai, 0700);
            }
            if ($user->avatar) {
                if (file_exists($user->avatar)) {
                    unlink($user->avatar);
                }
            }
            if (!file_exists($diretorioAvatar)) {
                mkdir($diretorioAvatar, 0700);
            }

            file_put_contents($urlAvatar, $file);

            $user->avatar = URL::to($urlAvatar);
        }

        $user->save();

        $user->avatar = asset($user->avatar);

        $user->token = $user->createToken($user->email)->accessToken;

        return ['status' => true, 'user' => $user];
    }


}
