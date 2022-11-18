<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLog;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use hisorange\BrowserDetect\Parser as Browser;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login', [
            'title' => 'SD Pemuda | Login'
        ]);
    }

    public function login(Request $request)
    {
        $inputan = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $level = DB::table('users')->where('email', $inputan['email'])->value('level');

        $client = new Client(); //GuzzleHttp\Client
        $url = "https://geolocation-db.com/json/geoip.php";


        $response = $client->request('GET', $url, [
            'verify'  => false,
        ]);

        $responseBody = json_decode($response->getBody());

        if ($level == 'admin') {
            if (Auth::attempt($inputan)) {
                $request->session()->regenerate();
                try {

                    $userLog = new UserLog();
                    $userLog->name = auth()->user()->name;
                    $userLog->country_name = $responseBody->country_name;
                    $userLog->city_name = $responseBody->city;
                    $userLog->ip = $responseBody->IPv4;
                    $userLog->state_name = $responseBody->state;
                    $userLog->latitude = $responseBody->latitude;
                    $userLog->longitude = $responseBody->longitude;
                    $userLog->device = Browser::browserName();
                    $userLog->level = auth()->user()->level;
                    $userLog->save();

                    return redirect()->intended('/dashboard');
                } catch (\Throwable $th) {
                    $request->session()->invalidate();

                    $request->session()->regenerateToken();
                    return back()->with('errorLogin', 'Login Gagal !');
                }
            }

            return back()->with('errorLogin', 'Login Gagal !');
        } elseif (Auth::attempt($inputan)) {
            $request->session()->regenerate();
            try {

                $userLog = new UserLog();
                $userLog->name = auth()->user()->name;
                $userLog->country_name = $responseBody->country_name;
                $userLog->city_name = $responseBody->city;
                $userLog->ip = $responseBody->IPv4;
                $userLog->state_name = $responseBody->state;
                $userLog->latitude = $responseBody->latitude;
                $userLog->longitude = $responseBody->longitude;
                $userLog->device = Browser::browserName();
                $userLog->level = auth()->user()->level;
                $userLog->save();

                return redirect()->intended('/');
            } catch (\Throwable $th) {
                $request->session()->invalidate();

                $request->session()->regenerateToken();
                return back()->with('errorLogin', 'Login Gagal !');
            }
        }

        return back()->with('errorLogin', 'Login Gagal !');
    }

    public function logout(Request $request)
    {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
