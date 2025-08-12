<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Notifications\CustomerEmailOtp;
use Illuminate\Support\Facades\Hash as FacadesHash;

class CustomerAuthController extends Controller
{
    /**
     * Show the customer login form
     */
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    /**
     * Handle customer login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt login (only if verified)
        $credentials = $request->only('email', 'password');

        if (Auth::guard('customer')->attempt($credentials, $request->filled('remember'))) {
            // Update last login timestamp
            $customer = Auth::guard('customer')->user();
            if (method_exists($customer, 'hasVerifiedEmail') && ! $customer->hasVerifiedEmail()) {
                // Keep them logged in; redirect to verification OTP notice
                return redirect()->route('customer.verification.otp.form')
                    ->with('status', 'verification-required');
            }
            if ($customer->must_change_password ?? false) {
                return redirect()->route('customer.password.force.form');
            }
            $customer->update([
                'last_login_at' => now(),
            ]);

            $request->session()->regenerate();
            return redirect()->intended(route('customer.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show forced password change form
     */
    public function showForceChangePasswordForm()
    {
        return view('customer.auth.force-change-password');
    }

    /**
     * Handle forced password change
     */
    public function forceChangePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::guard('customer')->user();
        if (! $user) {
            return redirect()->route('customer.login');
        }

        $user->forceFill([
            'password' => FacadesHash::make($request->password),
            'must_change_password' => false,
        ])->save();

        return redirect()->route('customer.dashboard')->with('success', 'Password updated.');
    }

    /**
     * Show customer registration form
     */
    public function showRegistrationForm()
    {
        return view('customer.auth.register');
    }

    /**
     * Handle customer registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customer_logins',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'nic' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Create customer record
        $customer = Customer::create([
            'custom_id' => Customer::generateCustomID(),
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'nic' => $request->nic,
            'address' => $request->address,
            'status' => true,
        ]);

        // Create login credentials
        $customerLogin = CustomerLogin::create([
            'customer_custom_id' => $customer->custom_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Log in, generate OTP for email verification, and redirect to OTP page
        Auth::guard('customer')->login($customerLogin);

        $otp = (string) random_int(100000, 999999);
        $customerLogin->forceFill([
            'email_verification_otp' => $otp,
            'email_verification_otp_expires_at' => now()->addMinutes(10),
        ])->save();

        $customerLogin->notify(new CustomerEmailOtp($otp, 'verify'));

        return redirect()->route('customer.verification.otp.form')
            ->with('status', 'otp-sent');
    }

    /**
     * Show email verification OTP form
     */
    public function showVerificationOtpForm()
    {
        return view('customer.auth.verify-otp');
    }

    /**
     * Verify email via OTP
     */
    public function verifyEmailWithOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = Auth::guard('customer')->user();
        if (! $user) {
            return redirect()->route('customer.login');
        }

        if (
            $user->email_verification_otp === $request->otp &&
            $user->email_verification_otp_expires_at &&
            now()->lte($user->email_verification_otp_expires_at)
        ) {
            $user->forceFill([
                'email_verified_at' => now(),
                'email_verification_otp' => null,
                'email_verification_otp_expires_at' => null,
            ])->save();

            return redirect()->route('customer.dashboard')->with('success', 'Email verified successfully.');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    /**
     * Resend verification OTP
     */
    public function resendVerificationOtp()
    {
        $user = Auth::guard('customer')->user();
        if (! $user) {
            return redirect()->route('customer.login');
        }

        $otp = (string) random_int(100000, 999999);
        $user->forceFill([
            'email_verification_otp' => $otp,
            'email_verification_otp_expires_at' => now()->addMinutes(10),
        ])->save();

        $user->notify(new CustomerEmailOtp($otp, 'verify'));

        return back()->with('status', 'otp-sent');
    }

    /**
     * Show passwordless login form (request OTP)
     */
    public function showOtpLoginRequestForm()
    {
        return view('customer.auth.login-otp-request');
    }

    /**
     * Send login OTP
     */
    public function sendLoginOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = CustomerLogin::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'We can\'t find a customer with that email address.']);
        }

        $otp = (string) random_int(100000, 999999);
        $user->forceFill([
            'login_otp' => $otp,
            'login_otp_expires_at' => now()->addMinutes(10),
        ])->save();

        $user->notify(new CustomerEmailOtp($otp, 'login'));

        return redirect()->route('customer.login.otp.form', ['email' => $user->email])->with('status', 'otp-sent');
    }

    /**
     * Show login OTP verify form
     */
    public function showLoginOtpForm(Request $request)
    {
        $email = $request->query('email');
        return view('customer.auth.login-otp-verify', compact('email'));
    }

    /**
     * Verify login OTP and sign in
     */
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $user = CustomerLogin::where('email', $request->email)->first();
        if (! $user || ! $user->login_otp || ! $user->login_otp_expires_at) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        if ($user->login_otp === $request->otp && now()->lte($user->login_otp_expires_at)) {
            // Clear OTP and login
            $user->forceFill([
                'login_otp' => null,
                'login_otp_expires_at' => null,
            ])->save();

            Auth::guard('customer')->login($user);

            // If email not verified, redirect to email-verify OTP page
            if (method_exists($user, 'hasVerifiedEmail') && ! $user->hasVerifiedEmail()) {
                return redirect()->route('customer.verification.otp.form')
                    ->with('status', 'otp-needed');
            }

            return redirect()->route('customer.dashboard');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    /**
     * Log the customer out
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }

    /**
     * Show the customer dashboard
     */
    public function dashboard()
    {
        $customer = Auth::guard('customer')->user()->customer;

        // Get relevant data for the customer dashboard
        $recentInvoices = $customer->salesInvoices()->latest()->limit(5)->get();
        $creditBalance = $customer->balance_credit;

        $vehicles = $customer->vehicles()->with(['serviceSchedule'])->get();
        $showVehicleQr = $vehicles->count() < 2;

        return view('customer.dashboard', compact('customer', 'recentInvoices', 'creditBalance', 'vehicles', 'showVehicleQr'));
    }
}
