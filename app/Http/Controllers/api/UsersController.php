<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Models\State;
use Config;
use App\Models\Lookup;
use App\Models\User;
use App\Models\UserDeviceToken;
use App\Models\Cms;
use Carbon\Carbon;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use App\Models\Temple;
use App\Models\Festival;
use App\Models\Faq;
use App\Models\FestivalFaq;
use App\Models\NotificationSetting;
use App\Models\Notification;
use App\Models\FestivalTemple;
use GuzzleHttp\Client;
use App\Models\Reminder;
use Redirect, Session, Auth;
use App\Models\Tiptap;

class UsersController extends Controller
{

    public function cms($type)
    {

        $cms = Cms::where('slug', $type)->with('cmsDesc')->first();
        $response = array();
        $response["status"] = "success";
        $response["data"] = $cms;
        $response["msg"] = trans("");
        return response()->json($response, 200);

    }

    public function faqs()
    {
        $faqs = Faq::with('faqDesc')->get();
        $response = array();
        $response["status"] = "success";
        $response["data"] = $faqs;
        $response["msg"] = trans("");
        return response()->json($response, 200);
    }


    public function signup(Request $request)
    {
        $formData = $request->all();
        if (!empty($formData)) {

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'phone_number' => 'required',
                ],
                [
                    "name.required" => trans("The full name field is required"),
                    "email.required" => trans("The email field is required"),
                    "email.email" => trans("The email must be type of email"),
                    "email.unique" => trans("The email entered is already registered"),
                    "phone_number.required" => trans("The phone number field is required"),
                ]
            );

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages()]);
            } else {
                $obj = new User;
                $obj->name = $request->input('name');
                $obj->email = $request->input('email');
                $obj->phone_prefix = $request->input('phone_prefix');
                $obj->phone_country_code = $request->input('phone_country_code');
                $obj->phone_number = $request->input('phone_number');
                $obj->country = $request->input('country');
                $obj->state = $request->input('state');
                $obj->language = $request->input('language');
                $obj->notify = $request->input('notify');


                $obj->is_verified = 1;

                $obj->save();

                //    if ($request->device_type && $request->device_id) {
                UserDeviceToken::create([
                    'user_id' => $obj->id,
                    'device_type' => $request->device_type,
                    'device_id' => $request->device_id
                ]);
                // }


                $token = $obj->createToken('API Token')->accessToken;


                $user = User::find($obj->id);

                return response()->json([
                    'status' => 'success',
                    'message' => trans('Signup successful, you are now logged in'),
                    'token' => $token,
                    'user' => $user
                ]);
            }
        } else {
            return response()->json(['msg' => trans('Something went wrong, please try again')]);
        }
    }


    public function getVerificationCode()
    {
        return 1234;
    }

    public function check_verification_token(Request $request, $validate_string)
    {
        $userInfo = User::where('forgot_password_validate_string', $validate_string)->first();
        if (empty($userInfo)) {
            $response["status"] = "error";
            $response["msg"] = trans("Invalid validate string");
            $response["data"] = (object) array();
            return json_encode($response);
        }
        $formData = $request->all();
        $response = array();
        if (!empty($formData)) {
            $request->replace($this->arrayStripTags($request->all()));
            $validator = Validator::make(
                $request->all(),
                array(
                    'verification_code' => 'required',
                ),
                array(
                    "verification_code.required" => trans("The verification code field is required"),
                )
            );
            if ($validator->fails()) {
                $response = $this->change_error_msg_layout($validator->errors()->getMessages());
            } else {
                $currentTime = date("Y-m-d H:i:s");
                $users = User::where("forgot_password_validate_string", $validate_string)
                    ->where("verification_code", $request->input("verification_code"))
                    ->select("id")
                    ->first();
                if (!empty($users)) {
                    User::where("id", $users->id)->update(array("verification_code" => NULL, "forgot_password_validate_string" => "", "is_verified" => 1));
                    if (!empty($request->input('device_token'))) {
                        User::where('id', $users->id)->update(['device_token' => $request->input('device_token'), 'device_type' => $request->input('device_type')]);
                    }
                    $user_details = User::where("id", $users->id)->first();
                    $user_details->profile_link = $user_details->profile_link;
                    $token = $user_details->createToken('Reymend Personal Access Client')->accessToken;
                    $response["status"] = "success";
                    $response["msg"] = trans("Your profile has been created successfully");
                    $response["data"] = $user_details;
                    $response["type"] = 'login';
                    $response["token"] = $token;
                    $userToken = UserDeviceToken::where('user_id', $user_details->id)->first();
                    // $this->send_push_notification($userToken->device_id,$userToken->device_type,"Resgistered Successfully",'registered',$user_details,'Registered User');
                    return json_encode($response);
                } else {
                    $response["status"] = "error";
                    $response["msg"] = trans("Invalid verification code");
                    $response["data"] = (object) array();
                    return json_encode($response);
                }
            }
        } else {
            $response["status"] = "error";
            $response["msg"] = trans("Invalid request");
            $response["data"] = (object) array();
            return json_encode($response);
        }
        return json_encode($response);
    }


    public function login(Request $request)
    {
        $response = [];

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
            ],
            [
                "email.required" => "The email field is required",
                "email.email" => "The email must be a valid email address",
            ]
        );

        if ($validator->fails()) {
            $response = $this->change_error_msg_layout($validator->errors()->getMessages());
        } else {
            $email = $request->input("email");
            $user_details = User::where("email", $email)->first();

            if ($user_details) {

                Auth::loginUsingId($user_details->id);
                $user_details = User::find($user_details->id);

                $token = $user_details->createToken('DirectLoginToken')->accessToken;
                if ($request->device_type && $request->device_id) {
                    $deviceToken = UserDeviceToken::where('user_id', $user_details->id)
                        ->where('device_id', $request->device_id)
                        ->first();

                    if ($deviceToken) {
                        // Update existing device token
                        $deviceToken->update([
                            'device_type' => $request->device_type,
                            'device_id' => $request->device_id,
                            'updated_at' => now()
                        ]);
                    } else {
                        // Create new device token
                        UserDeviceToken::create([
                            'user_id' => $user_details->id,
                            'device_type' => $request->device_type,
                            'device_id' => $request->device_id
                        ]);
                    }
                }
                $response["status"] = "success";
                $response["redirect"] = "redirect_Home";
                $response["msg"] = "You are now logged in";
                $response["data"] = $user_details;
                $response["token"] = $token;

            } else {

                $response["status"] = "success";
                $response["redirect"] = "redirect_signup";
                $response["msg"] = "User not found. Please sign up.";
                $response["data"] = (object) [];
            }
        }

        return response()->json($response);
    }


    public function temples()
    {

        $temples = Temple::where('is_deleted', 0)->with('templeDesc')->get();
        foreach ($temples as $temple) {
            $temple->image = config('constants.TEMPLE_IMAGE_PATH') . $temple->image;
        }
        $response = array();
        $response["status"] = "success";
        $response["data"] = $temples;
        $response["msg"] = trans("");
        return response()->json($response, 200);
    }



    public function festivals()
    {
        $userId = auth('api')->user()->id;

        $festivals = Festival::with('faqs.faqDesc', 'temple.templeDesc', 'festivalDesc')
            ->where('is_deleted', 0)
            ->get();

        $finalFestivals = collect();

        foreach ($festivals as $festival) {

            $festival->image = config('constants.FESTIVAL_IMAGE_PATH') . $festival->image;


            $templeIds = json_decode($festival->temple_id, true);

            if (is_array($templeIds) && count($templeIds) > 0) {
                $temples = Temple::whereIn('id', $templeIds)
                    ->where('is_deleted', 0)
                    ->get();


                foreach ($temples as $temple) {
                    $temple->image = config('constants.TEMPLE_IMAGE_PATH') . $temple->image;
                }

                $festival->temples = $temples;
            } else {
                $festival->temples = [];
            }
            //     $reminderExists = Reminder::where('festival_id', $festival->id)
            //     ->where('user_id', $userId)
            //     ->exists();

            //    $festival->is_remainder = $reminderExists ? 1 : 0;


            $faqs = FestivalFaq::where('festival_id', $festival->id)->get();
            $festival->faqs = $faqs;

            $dates = array_map('trim', explode(',', $festival->date));
            foreach ($dates as $singleDate) {
                $festivalCopy = clone $festival;
                $festivalCopy->date = $singleDate;

                // 🔥 Check reminder for specific festival_id + date
                $reminderExists = Reminder::where('festival_id', $festival->id)
                    ->where('user_id', $userId)
                    ->where('date', $singleDate)   // ⬅ KEY FIX
                    ->exists();

                $festivalCopy->is_remainder = $reminderExists ? 1 : 0;

                $finalFestivals->push($festivalCopy);
            }
        }


        $finalFestivals = $finalFestivals->sortBy(function ($festival) {
            return \Carbon\Carbon::parse($festival->date);
        })->values();

        $response = [
            "status" => "success",
            "data" => $finalFestivals,
            "msg" => trans(""),
        ];

        return response()->json($response, 200);
    }



    // public function festivalstab()
// {
//     $festivals = Festival::with('faqs.faqDesc', 'temple.templeDesc', 'festivalDesc')
//         ->where('is_deleted', 0)
//         ->get();

    //     $singleFestivals = collect();
//     $multipleFestivals = collect();

    //     foreach ($festivals as $festival) {
//         // Festival image
//         $festival->image = config('constants.FESTIVAL_IMAGE_PATH') . $festival->image;

    //         // Temple handling
//         $templeIds = json_decode($festival->temple_id, true);
//         if (is_array($templeIds) && count($templeIds) > 0) {
//             $temples = Temple::whereIn('id', $templeIds)
//                 ->where('is_deleted', 0)
//                 ->get();

    //             foreach ($temples as $temple) {
//                 $temple->image = config('constants.TEMPLE_IMAGE_PATH') . $temple->image;
//             }

    //             $festival->temples = $temples;
//         } else {
//             $festival->temples = [];
//         }

    //         // FAQs
//         $faqs = FestivalFaq::where('festival_id', $festival->id)->get();
//         $festival->faqs = $faqs;

    //         // Dates
//         $dates = array_map('trim', explode(',', $festival->date));

    //         if (count($dates) === 1) {
//             // single-date festival
//             $festival->date = $dates[0];
//             $singleFestivals->push($festival);
//         } else {
//             // multi-date festival
//             $festival->date = $dates[0]; // first date as main
//             $festival->festival_dates = $dates;
//             $multipleFestivals->push($festival);
//         }
//     }

    //     // Sorting both groups
//     $singleFestivals = $singleFestivals->sortBy(function ($festival) {
//         return \Carbon\Carbon::parse($festival->date);
//     })->values();

    //     $multipleFestivals = $multipleFestivals->sortBy(function ($festival) {
//         return \Carbon\Carbon::parse($festival->date);
//     })->values();

    //     $response = [
//         "status" => "success",
//         "data"   => [
//             "single_festivals"   => $singleFestivals,
//             "multiple_festivals" => $multipleFestivals,
//         ],
//         "msg"    => trans(""),
//     ];

    //     return response()->json($response, 200);
// }


    public function festivalstab(Request $request)
    {
        // $response = $this->send_push_notification(
        //     "dpw0F2QUQB6JPgPS7orkZA:APA91bH889rx0m5fCgCVFtQPri2bcdsl7mDzMkl65V7bSXzA2KmenPX6oxhhGg8quuD1RYy91qdOiUJTzA-DD4mNpXWxeaUPjp_lTcozEzx4k4hODyHj0gk",
        //     "android",
        //     "This is a test push",
        //     "Test Title",
        //     "test_notification",
        //     ["extra" => "data"]
        // );

        // dd($response);
        $userId = auth('api')->user()->id;

        $festivalsQuery = Festival::with('faqs.faqDesc', 'temple.templeDesc', 'festivalDesc')
            ->where('is_deleted', 0);
        // ->get();

        $singleFestivals = collect();
        $multipleFestivals = collect();
        if ($request->has('state_id') && !empty($request->state_id)) {
            $stateId = $request->state_id;
            $festivalsQuery->whereJsonContains('states', $stateId);
        }

        //   if ($request->has('state_id') && !empty($request->state_id)) {
        //     $stateId = $request->state_id;

        //     // If state_id is an array (multiple states), use whereJsonContains for each
        //     if (is_array($stateId)) {
        //         $festivalsQuery->where(function($query) use ($stateId) {
        //             foreach ($stateId as $state) {
        //                 $query->orWhereJsonContains('states', (int)$state);
        //             }
        //         });
        //     } else {
        //         // Single state filter
        //         $festivalsQuery->whereJsonContains('states', (int)$stateId);
        //     }
        // }
        $festivals = $festivalsQuery->get();
        foreach ($festivals as $festival) {

            // Festival image
            $festival->image = config('constants.FESTIVAL_IMAGE_PATH') . $festival->image;

            // Temple handling
            $templeIds = json_decode($festival->temple_id, true);
            if (is_array($templeIds) && count($templeIds) > 0) {
                $temples = Temple::whereIn('id', $templeIds)
                    ->where('is_deleted', 0)
                    ->get();

                foreach ($temples as $temple) {
                    $temple->image = config('constants.TEMPLE_IMAGE_PATH') . $temple->image;
                }

                $festival->temples = $temples;
            } else {
                $festival->temples = [];
            }

            // FAQs
            $faqs = FestivalFaq::where('festival_id', $festival->id)->get();
            $festival->faqs = $faqs;

            $reminderExists = Reminder::where('festival_id', $festival->id)
                ->where('user_id', $userId)
                ->exists();

            $festival->is_remainder = $reminderExists ? 1 : 0;


            // Dates
            $dates = array_map('trim', explode(',', $festival->date));

            if (count($dates) === 1) {
                $festival->date = $dates[0];
                $festival->is_multiple_festival = 0;
                $singleFestivals->push($festival);
            } else {
                $festival->date = $dates[0]; // first date as main
                $festival->festival_dates = $dates;
                $festival->is_multiple_festival = 1;
                $multipleFestivals->push($festival);
            }
        }

        // Sorting both groups
        $singleFestivals = $singleFestivals->sortBy(function ($festival) {
            return Carbon::parse($festival->date);
        })->values();

        $multipleFestivals = $multipleFestivals->sortBy(function ($festival) {
            return Carbon::parse($festival->date);
        })->values();

        $response = [
            "status" => "success",
            "data" => [
                "single_festivals" => $singleFestivals,
                "multiple_festivals" => $multipleFestivals,
            ],
            "msg" => "",
        ];

        return response()->json($response, 200);
    }


    public function festivalDetail($id)
    {
        $festival = Festival::where('id', $id)
            ->with(['faqs.faqDesc', 'festivalDesc'])
            ->where('is_deleted', 0)
            ->first();

        if ($festival) {
            $festival->image = config('constants.FESTIVAL_IMAGE_PATH') . $festival->image;

            // fetch temples manually through FestivalTemple
            $temples = FestivalTemple::with('temple.templeDesc')
                ->where('festival_id', $festival->id)
                ->get();

            foreach ($temples as $value) {
                $value->temple->image = config('constants.TEMPLE_IMAGE_PATH') . $value->temple->image;
            }

            $festival->temples = $temples; // attach temples into response
        }

        $response = [
            "status" => "success",
            "data" => $festival,
            "msg" => "",
        ];

        return response()->json($response, 200);
    }


    public function updateProfile(Request $request)
    {
        $formData = $request->all();
        if (!empty($formData)) {

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required',
                    'phone_number' => 'required',
                ],
                [
                    "name.required" => trans("The full name field is required"),
                    "email.required" => trans("The email field is required"),
                    "email.email" => trans("The email must be type of email"),
                    "email.unique" => trans("The email entered is already registered"),
                    "phone_number.required" => trans("The phone number field is required"),
                ]
            );

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages()]);
            } else {
                $obj = auth('api')->user();
                $obj->name = $request->input('name');
                $obj->email = $request->input('email');
                $obj->phone_prefix = $request->input('phone_prefix');
                $obj->phone_country_code = $request->input('phone_country_code');
                $obj->phone_number = $request->input('phone_number');
                $obj->country = $request->input('country');
                $obj->state = $request->input('state');
                $obj->language = $request->input('language');
                $obj->notify = $request->input('notify');

                if ($request->hasFile('image')) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $fileName = time() . '-image.' . $extension;
                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config('constants.USER_IMAGE_ROOT_PATH') . $folderName;

                    if ($request->file('image')->move($folderPath, $fileName)) {
                        $obj->image = $folderName . $fileName;
                    }
                }

                $obj->save();


                return response()->json([
                    'status' => 'success',
                    'message' => trans('Profile updated successfully'),
                    'user' => $obj
                ]);
            }
        } else {
            return response()->json(['msg' => trans('Something went wrong, please try again')]);
        }
    }

    public function deleteAccount()
    {
        User::where('id', auth('api')->user()->id)->delete();
        return response()->json([
            'status' => 'success',
            'message' => trans('Profile deleted'),
            'user' => $obj
        ]);
    }

    public function getPanchang(Request $request)
    {

        $postData = [
            "year" => (int) $request->year,
            "month" => (int) $request->month,
            "date" => (int) $request->date,
            "hours" => (int) $request->hours,
            "minutes" => (int) $request->minutes,
            "seconds" => (int) $request->seconds,
            "latitude" => (float) $request->lat,
            "longitude" => (float) $request->lng,
            "timezone" => (float) $request->timezone,
            "config" => [
                "observation_point" => "topocentric",
                "ayanamsha" => "lahiri"
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://json.freeastrologyapi.com/tithi-durations',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                //  'x-api-key: Tir8k0Wi1a2hDnD60J86v4Obcgtl7mCSJmh77hg4'
                'x-api-key: ysyYvW6Nvv6CtF1q043DH92w9d6JcMfN1KVcDiu8'

            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);


        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://json.freeastrologyapi.com/nakshatra-durations',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                //  'x-api-key: Tir8k0Wi1a2hDnD60J86v4Obcgtl7mCSJmh77hg4'
                'x-api-key: ysyYvW6Nvv6CtF1q043DH92w9d6JcMfN1KVcDiu8'

            ],
        ]);

        $response2 = curl_exec($curl);
        curl_close($curl);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://json.freeastrologyapi.com/yama-gandam',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                // 'x-api-key: Tir8k0Wi1a2hDnD60J86v4Obcgtl7mCSJmh77hg4'
                'x-api-key: ysyYvW6Nvv6CtF1q043DH92w9d6JcMfN1KVcDiu8'

            ],
        ]);

        $response3 = curl_exec($curl);
        curl_close($curl);

        $tithi = json_decode($response, true);
        $nakshatra = json_decode($response2, true);
        $yamaGandam = json_decode($response3, true);

        // Each contains "output" which is also JSON string → decode again
        $tithiOutput = isset($tithi['output']) ? json_decode($tithi['output'], true) : null;
        $nakshatraOutput = isset($nakshatra['output']) ? json_decode($nakshatra['output'], true) : null;
        $yamaGandamOutput = isset($yamaGandam['output']) ? json_decode($yamaGandam['output'], true) : null;

        return response()->json([
            'success' => true,
            'tithi' => $tithiOutput,
            'nakshatra' => $nakshatraOutput,
            'yama_gandam' => $yamaGandamOutput
        ]);

    }


    public function createReminder(Request $request)
    {
        $request->validate([
            'festival_id' => 'required|integer',
            'festival_date' => 'required|string',           // can be single or CSV
            'before_days' => 'required|integer|min:0',
            'time' => 'nullable|date_format:H:i',
            'is_recurring' => 'required|in:0,1'
        ]);

        $userId = auth('api')->user()->id;

        // Default time
        $reminderTime = $request->time ?? "00:00";

        // If recurring = 1 → festival_date is CSV list
        if ($request->is_recurring == 1) {

            $festival_dates = Festival::where('id', $request->festival_id)->value('date');
            $dates = explode(',', $festival_dates); // multiple dates

            $createdReminders = [];

            foreach ($dates as $date) {
                $date = trim($date);

                // Skip invalid dates
                if (!strtotime($date)) {
                    continue;
                }

                $festivalDate = Carbon::parse($date);

                // Calculate reminder date = festival date - before_days
                $reminderDate = $festivalDate->copy()->subDays($request->before_days);

                // Combine date + time
                $reminderDateTime = Carbon::parse($reminderDate->format('Y-m-d') . " " . $reminderTime);

                // Save reminder
                $createdReminders[] = Reminder::create([
                    'user_id' => $userId,
                    'festival_id' => $request->festival_id,
                    'date' => $festivalDate,
                    'time' => $reminderDateTime,
                    'is_recurring' => 1,
                ]);
            }

            return response()->json([
                'message' => 'Recurring reminders created successfully.',
                'data' => $createdReminders,
            ], 201);
        }

        // === Non-recurring (Normal Single Reminder) ===

        $festivalDate = Carbon::parse($request->festival_date);
        $reminderDate = $festivalDate->copy()->subDays($request->before_days);

        $reminderDateTime = Carbon::parse($reminderDate->format('Y-m-d') . " " . $reminderTime);

        $reminder = Reminder::create([
            'user_id' => $userId,
            'festival_id' => $request->festival_id,
            'date' => $festivalDate,
            'time' => $reminderDateTime,
            'is_recurring' => 0,
        ]);

        return response()->json([
            'message' => 'Reminder created successfully.',
            'data' => $reminder,
        ], 201);
    }


    public function getReminder()
    {
        $userId = auth('api')->user()->id;
        $reminders = Reminder::where('user_id', $userId)->with('festival')->get();

        return response()->json([
            'message' => 'Reminder retreived successfully.',
            'data' => $reminders,
        ], 201);
    }

    public function deleteReminder($festival_id)
    {

        $userId = auth('api')->user()->id;
        $reminders = Reminder::where('id', $festival_id)->delete();

        return response()->json([
            'message' => 'Reminder deleted successfully.',
            'data' => [],
        ], 201);
    }

    public function getManageNotificationPublic()
    {
        $user = User::first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'No users found'], 404);
        }

        $settings = NotificationSetting::where('user_id', $user->id)->first();

        return response()->json([
            'status' => true,
            'message' => 'Notification settings (Public Test) fetched successfully',
            'data' => $settings,
            'test_user' => $user->email
        ]);
    }

    public function getManageNotification()
    {
        $userId = auth('api')->user()->id;

        $settings = NotificationSetting::where('user_id', $userId)->first();

        return response()->json([
            'status' => true,
            'message' => 'Notification settings fetched successfully',
            'data' => $settings
        ]);
    }

    public function notificationsListPublic()
    {
        // For testing purposes, we use user ID 1 or the first user in the DB
        $user = User::first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'No users found'], 404);
        }

        $notifications = Notification::where('user_id', $user->id)->get();

        return response()->json([
            'status' => true,
            'message' => 'Notification List (Public Test) fetched successfully',
            'data' => $notifications,
            'test_user' => $user->email
        ]);
    }

    public function notificationsList()
    {
        $userId = auth('api')->user()->id;

        //$settings = Notification::where('user_id', $userId)->get();
        $settings = Notification::where('user_id', $userId)->get();

        return response()->json([
            'status' => true,
            'message' => 'Notification List fetched successfully',
            'data' => $settings
        ]);
    }

    public function updateManageNotification(Request $request)
    {
        $request->validate([
            'daily_panchang' => 'nullable|boolean',
            'festival_notification' => 'nullable|boolean'
        ]);
        $userId = auth('api')->user()->id;

        $settings = NotificationSetting::updateOrCreate(
            ['user_id' => $userId], // condition
            [
                'daily_panchang' => $request->daily_panchang ?? 1,
                'festival_notification' => $request->festival_notification ?? 1
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Notification settings updated successfully',
            'data' => $settings
        ]);
    }
    public function tiptapIndex(Request $request)
    {
        try {
            $DB = Tiptap::query();
            $inputGet = $request->all();

            // Search and filter logic (same as your original)
            if ($request->all()) {
                $searchData = $request->all();
                unset($searchData['display']);
                unset($searchData['_token']);

                if (isset($searchData['order'])) {
                    unset($searchData['order']);
                }
                if (isset($searchData['sortBy'])) {
                    unset($searchData['sortBy']);
                }
                if (isset($searchData['page'])) {
                    unset($searchData['page']);
                }
                if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                    $dateS = date("Y-m-d", strtotime($searchData['date_from']));
                    $dateE = date("Y-m-d", strtotime($searchData['date_to']));
                    $DB->whereBetween('tiptaps.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
                } elseif (!empty($searchData['date_from'])) {
                    $dateS = $searchData['date_from'];
                    $DB->where('tiptaps.created_at', '>=', [$dateS . " 00:00:00"]);
                } elseif (!empty($searchData['date_to'])) {
                    $dateE = $searchData['date_to'];
                    $DB->where('tiptaps.created_at', '<=', [$dateE . " 00:00:00"]);
                }

                foreach ($searchData as $fieldName => $fieldValue) {
                    if ($fieldValue != "") {
                        if ($fieldName == "title") {
                            $DB->where("tiptaps.title", 'like', '%' . $fieldValue . '%');
                        }
                    }
                }
            }

            $DB->where("tiptaps.is_deleted", 0);

            $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'tiptaps.created_at';
            $order = ($request->input('order')) ? $request->input('order') : 'DESC';

            // For API, you can set a default or allow per_page parameter
            $records_per_page = ($request->input('per_page')) ? $request->input('per_page') : 10;

            // Get paginated results
            $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);

            // Transform results to include full image URLs
            $transformedResults = $results->getCollection()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'image' => $item->image ? Config::get('constants.TIPTAP_IMAGE_PATH') . $item->image : null,
                    'thumbnail' => $item->image ? Config::get('constants.TIPTAP_IMAGE_PATH') . $item->image : null,
                    'image_name' => $item->image,
                    'status' => $item->status,
                    'is_deleted' => $item->is_deleted,
                    'created_at' => $item->created_at ? Carbon::parse($item->created_at)->toDateTimeString() : null,
                    'updated_at' => $item->updated_at ? Carbon::parse($item->updated_at)->toDateTimeString() : null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Tiptaps fetched successfully',
                'data' => [
                    'current_page' => $results->currentPage(),
                    'data' => $transformedResults,
                    'first_page_url' => $results->url(1),
                    'from' => $results->firstItem(),
                    'last_page' => $results->lastPage(),
                    'last_page_url' => $results->url($results->lastPage()),
                    'next_page_url' => $results->nextPageUrl(),
                    'path' => $results->path(),
                    'per_page' => $results->perPage(),
                    'prev_page_url' => $results->previousPageUrl(),
                    'to' => $results->lastItem(),
                    'total' => $results->total(),
                ],
                'search_params' => $inputGet,
                'sort_by' => $sortBy,
                'order' => $order,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tiptaps',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function statesList(Request $request)
    {
        try {
            // Fetch all states from the database
            $states = State::all();

            // Check if states exist
            if ($states->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No states found',
                    'data' => []
                ], 404);
            }

            // Return successful response with states data
            return response()->json([
                'success' => true,
                'message' => 'States retrieved successfully',
                'data' => $states
            ], 200);

        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve states',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
