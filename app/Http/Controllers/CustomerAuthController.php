<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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

        // Attempt login
        $credentials = $request->only('email', 'password');
        
        if (Auth::guard('customer')->attempt($credentials, $request->filled('remember'))) {
            // Update last login timestamp
            $customer = Auth::guard('customer')->user();
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

        // Login the customer
        Auth::guard('customer')->login($customerLogin);

        return redirect()->route('customer.dashboard');
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
        
        return view('customer.dashboard', compact('customer', 'recentInvoices', 'creditBalance'));
    }
}
