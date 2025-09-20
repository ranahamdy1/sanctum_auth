<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OtpController extends Controller
{
    // إرسال OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email'
        ]);

        $otpCode = rand(100000, 999999);

        $otp = Otp::create([
            'email' => $request->email,
            'phone' => $request->phone,
            'otp' => $otpCode,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // هنا ممكن تبعت الكود بالإيميل أو SMS
        // مثال للإيميل:
        if($request->email){
            \Mail::raw("Your OTP code is: $otpCode", function($message) use ($request){
                $message->to($request->email)
                    ->subject('OTP Verification Code');
            });
        }

        return response()->json(['message' => 'OTP sent successfully']);
    }

    // التحقق من OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email',
            'otp' => 'required'
        ]);

        $otp = Otp::where(function($q) use ($request){
            if($request->email) $q->where('email', $request->email);
            if($request->phone) $q->where('phone', $request->phone);
        })->where('otp', $request->otp)
            ->where('expires_at', '>=', Carbon::now())
            ->first();

        if(!$otp){
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        return response()->json(['message' => 'OTP verified successfully']);
    }

    public function resetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // تحقق من OTP صالح
        $otp = \App\Models\Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>=', Carbon::now())
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        // تحديث الباسورد
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // مسح OTP بعد الاستخدام
        $otp->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }
}
