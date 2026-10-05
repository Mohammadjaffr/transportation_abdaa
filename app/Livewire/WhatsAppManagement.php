<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class WhatsAppManagement extends Component
{
    public array $status = [];
    public bool $loadingStatus = false;
    
    public string $linkMode = 'qr';
    public ?string $pairingPhone = null;
    public ?string $pairingCode = null;
    public ?string $qrCode = null;
    
    public string $testPhone = '';
    public string $testMessage = '';
    
    public array $instanceInfo = [];

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */
    public function mount(WhatsAppService $whatsAppService)
    {
        $this->refreshStatus($whatsAppService);
    }

    /*
    |--------------------------------------------------------------------------
    | State Extract & Polling
    |--------------------------------------------------------------------------
    */
    private function extractState(array $response): string
{
    if (!empty($response['error'])) {
        return 'error';
    }

    // استخراج القيم الأساسية من الاستجابة الرسمية
    $isLoggedIn  = data_get($response, 'data.LoggedIn')
        ?? data_get($response, 'data.loggedIn')
        ?? data_get($response, 'LoggedIn')
        ?? data_get($response, 'loggedIn');

    $isConnected = data_get($response, 'data.Connected')
        ?? data_get($response, 'data.connected')
        ?? data_get($response, 'Connected')
        ?? data_get($response, 'connected');

    // 1. الشرط الأساسي: لا يعتبر متصلاً إلا إذا كان مسجل الدخول بالفعل
    if ($isLoggedIn === true) {
        return 'connected';
    }

    // 2. إذا كان غير مسجل الدخول (false)، فهو مفصول وغير متصل
    if ($isLoggedIn === false) {
        return 'disconnected';
    }

    // 3. فحص الحالات النصية كإجراء احتياطي (في حال أعاد السيرفر نصوصاً)
    $rawState = data_get($response, 'data.state')
        ?? data_get($response, 'data.status')
        ?? data_get($response, 'instance.state')
        ?? data_get($response, 'instance.status')
        ?? data_get($response, 'status')
        ?? data_get($response, 'state')
        ?? 'unknown';

    $state = strtolower(trim((string)$rawState));

    switch ($state) {
        case 'open':
        case 'connected':
        case 'conectado':
        case 'online':
        case 'ready':
            return 'connected';

        case 'connecting':
        case 'conectando':
        case 'starting':
        case 'opening':
            return 'connecting';

        case 'close':
        case 'closed':
        case 'disconnected':
        case 'desconectado':
        case 'offline':
            return 'disconnected';

        default:
            return 'disconnected';
    }
}

    private function waitForConnectionState(
        WhatsAppService $whatsAppService,
        array $expectedStates,
        int $attempts = 5,
        int $delayMilliseconds = 800
    ): array {
        $lastState = 'unknown';
        $lastResponse = [];

        for ($i = 0; $i < $attempts; $i++) {
            $response = $whatsAppService->getStatus();
            $lastResponse = $response;

            if (isset($response['error'])) {
                $lastState = 'error';
            } else {
                $lastState = $this->extractState($response);
            }

            if (in_array($lastState, $expectedStates)) {
                return [
                    'matched' => true,
                    'state' => $lastState,
                    'response' => $lastResponse,
                ];
            }

            if ($i < $attempts - 1) {
                usleep($delayMilliseconds * 1000);
            }
        }

        return [
            'matched' => false,
            'state' => $lastState,
            'response' => $lastResponse,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Refresh Status
    |--------------------------------------------------------------------------
    */
    public function refreshStatus(WhatsAppService $whatsAppService): void
    {
        try {
            $this->loadingStatus = true;
            $response = $whatsAppService->getStatus();
            
            if (isset($response['error'])) {
                $this->status = [
                    'error'   => true,
                    'message' => $response['message'] ?? 'حدث خطأ غير معروف.',
                ];
            } else {
                $this->status = [
                    'state' => $this->extractState($response),
                ];
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp management status error', [
                'error' => $e->getMessage(),
            ]);

            $this->status = [
                'error'   => true,
                'message' => $e->getMessage(),
            ];
        } finally {
            $this->loadingStatus = false;
        }
    }

    public function getConnectionStateProperty(): array
    {
        if (isset($this->status['error'])) {
            $msg = $this->status['message'] ?? (is_string($this->status['error']) ? $this->status['error'] : 'تعذر الوصول للخدمة.');
            if ($msg === 'not authorized' || (is_string($msg) && str_contains(strtolower($msg), 'authorized'))) {
                $msg = 'غير مصرح: تحقق من مفتاح الاتصال (API Token) في الإعدادات.';
            } elseif ($this->status['error'] === true) {
                $msg = $this->status['message'] ?? 'تعذر الوصول للخدمة.';
            }
            return ['key' => 'error', 'label' => 'خطأ في الاتصال', 'color' => 'danger', 'icon' => 'fas fa-times-circle', 'message' => $msg];
        }

        $state = $this->status['state'] ?? 'unknown';

        switch ($state) {
            case 'connected':
                return ['key' => 'connected', 'label' => 'متصل', 'color' => 'success', 'icon' => 'fas fa-check-circle', 'message' => 'جلسة الواتساب متصلة وجاهزة للإرسال.'];
            case 'connecting':
                return ['key' => 'connecting', 'label' => 'جاري الاتصال', 'color' => 'warning', 'icon' => 'fas fa-spinner fa-spin', 'message' => 'يتم حاليًا إنشاء اتصال جلسة الواتساب.'];
            case 'disconnected':
                return ['key' => 'disconnected', 'label' => 'غير متصل', 'color' => 'secondary', 'icon' => 'fas fa-unlink', 'message' => 'جلسة الواتساب مفصولة حاليًا.'];
            case 'logged_out':
                return ['key' => 'logged_out', 'label' => 'تم تسجيل الخروج', 'color' => 'danger', 'icon' => 'fas fa-sign-out-alt', 'message' => 'يلزم إعادة ربط حساب الواتساب.'];
            default:
                return ['key' => 'unknown', 'label' => 'حالة غير معروفة', 'color' => 'secondary', 'icon' => 'fas fa-question-circle', 'message' => 'تعذر تحديد حالة الجلسة من استجابة الخادم.'];
        }
    }

    public function showQrTab()
    {
        $this->linkMode = 'qr';
    }

    public function showPairingTab()
    {
        $this->linkMode = 'pairing';
    }

    /*
    |--------------------------------------------------------------------------
    | Connect
    |--------------------------------------------------------------------------
    */
    public function connect(WhatsAppService $whatsAppService): void
    {
        $this->refreshStatus($whatsAppService);
        $currentState = $this->status['state'] ?? 'unknown';

        if ($currentState === 'connected') {
            $this->dispatch('show-toast', type: 'info', message: 'حساب الواتساب متصل بالفعل.');
            return;
        }

        $response = $whatsAppService->connect();

        if (isset($response['error'])) {
            $this->dispatch('show-toast', type: 'error', message: 'تعذر بدء اتصال الواتساب.');
            $this->refreshStatus($whatsAppService);
            return;
        }

        if ($currentState === 'logged_out') {
            $this->dispatch('show-toast', type: 'info', message: 'تم بدء جلسة الواتساب. يرجى ربط الحساب باستخدام QR Code أو كود الاقتران.');
            $this->refreshStatus($whatsAppService);
            return;
        }

        $verify = $this->waitForConnectionState($whatsAppService, ['connected']);

        if ($verify['state'] === 'connected') {
            $this->dispatch('show-toast', type: 'success', message: 'تم بدء اتصال الواتساب بنجاح.');
        } elseif ($verify['state'] === 'connecting') {
            $this->dispatch('show-toast', type: 'warning', message: 'تم بدء الجلسة، وجاري الاتصال بالواتساب.');
        } else {
            $this->dispatch('show-toast', type: 'warning', message: 'تم تنفيذ أمر بدء الاتصال، لكن الجلسة ما زالت غير متصلة.');
        }

        $this->refreshStatus($whatsAppService);
    }

    /*
    |--------------------------------------------------------------------------
    | QR Code
    |--------------------------------------------------------------------------
    */
    public function loadQrCode(WhatsAppService $whatsAppService): void
    {
        $response = $whatsAppService->getQrCode();

        $qrCodeData = $response['qr']
            ?? $response['qrcode']
            ?? $response['base64']
            ?? data_get($response, 'data.qr')
            ?? data_get($response, 'data.qrcode')
            ?? data_get($response, 'data.base64');

        if ($qrCodeData) {
            if (str_starts_with($qrCodeData, 'data:image') || filter_var($qrCodeData, FILTER_VALIDATE_URL)) {
                $this->qrCode = $qrCodeData;
            } elseif (base64_decode($qrCodeData, true) !== false) {
                $this->qrCode = 'data:image/png;base64,' . $qrCodeData;
            } else {
                $this->qrCode = $qrCodeData;
            }
        } else {
            $this->qrCode = null;
        }

        if (!$this->qrCode) {
            if (isset($response['error'])) {
                $this->dispatch('show-toast', type: 'error', message: 'تعذر الحصول على رمز QR.');
            } elseif (in_array($this->getConnectionStateProperty()['key'], ['connected', 'open'])) {
                $this->dispatch('show-toast', type: 'info', message: 'حساب الواتساب مرتبط بالفعل.');
            } else {
                $this->dispatch('show-toast', type: 'warning', message: 'تم استدعاء QR ولكن لم يتم العثور على صورة QR في الاستجابة.');
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Pairing Code
    |--------------------------------------------------------------------------
    */
    public function getPairingCode(WhatsAppService $whatsAppService): void
    {
        $this->validate([
            'pairingPhone' => 'required|string|max:25',
        ], [
            'pairingPhone.required' => 'يرجى إدخال رقم الهاتف.',
        ]);

        $response = $whatsAppService->getPairingCode($this->pairingPhone);

        $this->pairingCode = $response['code']
            ?? $response['pairingCode']
            ?? $response['pairing_code']
            ?? data_get($response, 'data.code')
            ?? data_get($response, 'data.pairingCode')
            ?? data_get($response, 'data.pairing_code');

        if ($this->pairingCode) {
            $this->dispatch('show-toast', type: 'success', message: 'تم إنشاء كود الاقتران.');
        } else {
            $this->dispatch('show-toast', type: 'error', message: 'تعذر إنشاء كود الاقتران.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Reconnect
    |--------------------------------------------------------------------------
    */
    public function reconnect(WhatsAppService $whatsAppService): void
    {
        $this->refreshStatus($whatsAppService);
        $currentState = $this->status['state'] ?? 'unknown';

        if ($currentState === 'connected') {
            $this->dispatch('show-toast', type: 'info', message: 'جلسة الواتساب متصلة بالفعل.');
            return;
        }

        $response = $whatsAppService->reconnect();

        if (isset($response['error'])) {
            $this->dispatch('show-toast', type: 'error', message: 'فشلت محاولة إعادة الاتصال.');
            $this->refreshStatus($whatsAppService);
            return;
        }

        $verify = $this->waitForConnectionState($whatsAppService, ['connected']);

        if ($verify['state'] === 'connected') {
            $this->dispatch('show-toast', type: 'success', message: 'تمت إعادة اتصال الواتساب بنجاح.');
        } elseif ($verify['state'] === 'connecting') {
            $this->dispatch('show-toast', type: 'warning', message: 'تم تنفيذ إعادة الاتصال، والجلسة ما زالت قيد الاتصال.');
        } else {
            $this->dispatch('show-toast', type: 'warning', message: 'تم تنفيذ طلب إعادة الاتصال، لكن الجلسة ما زالت غير متصلة.');
        }

        $this->refreshStatus($whatsAppService);
    }

    /*
    |--------------------------------------------------------------------------
    | Disconnect
    |--------------------------------------------------------------------------
    */
    public function disconnect(WhatsAppService $whatsAppService): void
    {
        $this->refreshStatus($whatsAppService);
        $currentState = $this->status['state'] ?? 'unknown';

        if (in_array($currentState, ['disconnected', 'logged_out'])) {
            $this->dispatch('show-toast', type: 'info', message: 'جلسة الواتساب مفصولة بالفعل.');
            return;
        }

        $response = $whatsAppService->disconnect();

        if (isset($response['error'])) {
            $this->dispatch('show-toast', type: 'error', message: 'تعذر فصل اتصال الواتساب.');
            $this->refreshStatus($whatsAppService);
            return;
        }

        // Increase attempts and wait time for disconnect as providers may take longer to drop the session
        $verify = $this->waitForConnectionState($whatsAppService, ['disconnected', 'logged_out'], 8, 1000);

        if (in_array($verify['state'], ['disconnected', 'logged_out'])) {
            $this->dispatch('show-toast', type: 'success', message: 'تم فصل اتصال الواتساب بنجاح.');
        } else {
            $this->dispatch('show-toast', type: 'warning', message: 'تم تنفيذ طلب الفصل، لكن الجلسة ما زالت متصلة. (' . $verify['state'] . ')');
        }

        $this->refreshStatus($whatsAppService);
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    public function logoutWhatsApp(WhatsAppService $whatsAppService): void
    {
        $response = $whatsAppService->logout();
        
        $this->qrCode = null;
        $this->pairingCode = null;
        $this->instanceInfo = [];

        if (isset($response['error'])) {
            $this->dispatch('show-toast', type: 'error', message: 'تعذر تسجيل خروج الواتساب.');
            $this->refreshStatus($whatsAppService);
            return;
        }

        $this->waitForConnectionState($whatsAppService, ['logged_out', 'disconnected']);

        $this->dispatch('show-toast', type: 'success', message: 'تم تسجيل خروج الواتساب بنجاح.');

        $this->refreshStatus($whatsAppService);
    }

    /*
    |--------------------------------------------------------------------------
    | Instance Info
    |--------------------------------------------------------------------------
    */
    public function loadInstanceInfo(WhatsAppService $whatsAppService): void
    {
        $response = $whatsAppService->getInstanceInfo();

        if (isset($response['error'])) {
            $this->dispatch('show-toast', type: 'error', message: 'تعذر جلب معلومات الحساب.');
            return;
        }

        $ownerJid = data_get($response, 'instance.ownerJid') ?? data_get($response, 'ownerJid') ?? data_get($response, 'number');
        if (is_string($ownerJid)) {
            $ownerJid = preg_replace('/(:.*|@s\.whatsapp\.net)$/', '', $ownerJid);
        }

        $this->instanceInfo = [
            'instanceName' => data_get($response, 'instance.instanceName') ?? data_get($response, 'instanceName'),
            'ownerJid' => $ownerJid,
            'profileName' => data_get($response, 'instance.profileName') ?? data_get($response, 'profileName'),
            'state' => data_get($response, 'instance.state') ?? data_get($response, 'state'),
        ];
        
        $this->dispatch('show-toast', type: 'success', message: 'تم تحديث معلومات الحساب.');
    }

    /*
    |--------------------------------------------------------------------------
    | Test Message
    |--------------------------------------------------------------------------
    */
    public function sendTestMessage(WhatsAppService $whatsAppService): void
    {
        $this->validate([
            'testPhone'   => 'required|string|max:25',
            'testMessage' => 'required|string|max:2000',
        ], [
            'testPhone.required'   => 'يرجى إدخال رقم الهاتف.',
            'testMessage.required' => 'يرجى كتابة الرسالة التجريبية.',
        ]);

        $response = $whatsAppService->sendText($this->testPhone, $this->testMessage);

        if (isset($response['error'])) {
            $this->dispatch('show-toast', type: 'error', message: 'فشل إرسال الرسالة التجريبية.');
            return;
        }

        $this->dispatch('show-toast', type: 'success', message: 'تم إرسال الرسالة التجريبية بنجاح.');
        $this->testPhone = '';
        $this->testMessage = '';
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        return view('livewire.whatsapp-management');
    }
}