<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Milly\Laragram\Laragram;
use Milly\Laragram\Types\KeyboardButton;

class BotController extends Controller
{
    public int|bool $step = 0;
    // Checking user - function
    public function checkUser($chat_id=0): bool
    {
        $user = User::where('telegram_id',$chat_id)
            ->get();
        return count($user) > 0;
    }

    public function generateLogin(): string
    {
        return Str::lower(Str::random(8)); // 8 belgi uzunligida tasodifiy login generatsiya qiladi
    }

    public function generatePassword(): string
    {
        return Str::random(12); // 12 belgi uzunligida tasodifiy parol generatsiya qiladi
    }

    public function fullname($firs_name="",$last_name=""): string
    {
        return $firs_name." ".$last_name;
    }

    public function userLink($username=""): string
    {
        return "https://t.me/".$username;
    }

    /**
     * @throws Exception
     */
    public function handle(Request $request): void
    {
        $input = $request->all();

        $message = $input['message'];
        $chat_id = $message['chat']['id'];
        try {

            $name = $this->fullname($message['from']['first_name'], $message['from']['last_name']);
            $user_link = $this->userLink($message['from']['username']);

            $user = User::where('telegram_id',$chat_id)
                ->first();
            if ($message && isset($message['text']) && $message['text'] == "/start") {
                Laragram::sendMessage(
                    $chat_id,
                    null,
                    "Asslomu alaykum, <a href='$user_link'>".$name. "</a>. Botga xush kelibsiz!. Sizning login parolingiz",
                    parse_mode: "HTML",
                    disable_web_page_preview: true
                );
            }
            elseif($this->checkUser($chat_id) && $user->step == 1){
                User::where('telegram_id',$chat_id)
                    ->update([
                        'first_name'    => $message['text'],
                        'step'          => 2
                    ]);

                Laragram::sendMessage(
                    $chat_id,
                    null,
                    "Ajoyib, Ismingizni kiriting: ",
                    parse_mode: "HTML",
                    disable_web_page_preview: true
                );
            }
            elseif($this->checkUser($chat_id) && $user->step == 2){
                User::where('telegram_id',$chat_id)
                    ->update([
                        'last_name'     => $message['text'],
                        'step'          => 3
                    ]);

                $buttons = json_encode(
                    [
                        'resize_keyboard'   => true,
                        'keyboard' => [
                            [
                                [
                                    'text'              => 'Raqamni yuborish',
                                    'request_contact'   => true,
                                ],
                            ]
                        ]]);

                Laragram::sendMessage(
                    chat_id: $chat_id,
                    text: "Telefon raqamni yuboring",
                    reply_markup: $buttons
                );
            }
            elseif($this->checkUser($chat_id) && $user->step == 3){
                $phone_number = $message["contact"]["phone_number"] ?? '';

                if($phone_number) {
                    User::where('telegram_id',$chat_id)
                        ->update([
                            'call'     => $phone_number,
                            'step'     => 4
                        ]);

                    $login = $this->generateLogin();
                    $password = $this->generatePassword();
                    if (!$user->login || !$user->password) {
                        $user->update([
                            'login'     => $login,
                            'password'  => $password,
                            'password_temp' => $password
                        ]);
                    }

                    Laragram::sendMessage(
                        $chat_id,
                        null,
                        "Tabriklaymiz. Botga xush kelibsiz!.\n<b>Login: $user->login</b>\n<b>Parol: $user->password_temp</b>",
                        parse_mode: "HTML",
                        disable_web_page_preview: true
                    );
                }
            }
            elseif($this->checkUser($chat_id) && $user->step == 4){
                $login = $this->generateLogin();
                $password = $this->generatePassword();
                if (!$user->login || !$user->password) {
                    $user->update([
                        'login'     => $login,
                        'password'  => $password,
                        'password_temp' => $password
                    ]);
                }

                Laragram::sendMessage(
                    $chat_id,
                    null,
                    "Asslomu alaykum, <a href='$user_link'>".$name. "</a>. Botga xush kelibsiz!.\n<b>Login: $user->login</b>\n<b>Parol: $user->password_temp</b>",
                    parse_mode: "HTML",
                    disable_web_page_preview: true
                );
            }

            else{
                if (!$this->checkUser($chat_id))
                {
                    User::create([
                        'telegram_id' => $chat_id,
                        'step'        => 1
                    ]);

                    Laragram::sendMessage(
                        $chat_id,
                        null,
                        "Asslomu alaykum, <a href='$user_link'>".$name. "</a>. Botga xush kelibsiz!. Siz ro'yhatdan o'tishingiz kerak.\n<b>Iltimos Familyangizni kiriting:</b>",
                        parse_mode: "HTML",
                        disable_web_page_preview: true
                    );
                }
            }
        }catch (Exception $e){
            Laragram::sendMessage($chat_id, null, "Xatolik: " . $e->getMessage());
        }
    }

    private function call($method, $params = [])
    {
        $url = "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/" . $method;
        $response = Http::post($url, $params);
        return $response->json();
    }
}
