<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Exception;

class WhatsAppService
{
    protected string $baseUrl;
    protected ?string $instanceId;
    protected ?string $token;
    protected ?string $webhookUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl    = rtrim(config('whatsapp.base_url'), '/');
        $this->instanceId = config('whatsapp.instance_id');
        $this->token      = config('whatsapp.instance_token');
        $this->webhookUrl = config('whatsapp.webhook_url');
        $this->timeout    = config('whatsapp.timeout', 30);
    }

    /**
     * إعداد عميل الـ HTTP بالـ Headers الأساسية
     */
    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->withHeaders([
                'Apikey'       => $this->token,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ]);
    }

    /* =========================================================================
     * 1. دوال التحكم بالإنستانس (Instance Management)
     * ========================================================================= */

    protected function handleResponse(Response $response): array
    {
        if ($response->failed()) {
            return [
                'error' => true,
                'message' => data_get($response->json(), 'message', data_get($response->json(), 'error', 'HTTP Error ' . $response->status())),
                'status' => $response->status()
            ];
        }
        return $response->json() ?? [];
    }

    /**
     * فحص حالة الاتصال الحالية للإنستانس
     * GET /instance/status
     */
    public function getStatus(): array
    {
        try {
            $response = $this->client()->get('/instance/status');
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService getStatus Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * بدء الاتصال وتحديد الـ Webhook
     * POST /instance/connect
     */
    public function connect(?string $webhookUrl = null, array $events = ['MESSAGES_UPSERT', 'CONNECTION_UPDATE', 'QRCODE_UPDATED']): array
    {
        try {
            $payload = [
                'webhookUrl' => $webhookUrl ?? $this->webhookUrl,
                'subscribe'  => $events,
                'immediate'  => true,
            ];

            $response = $this->client()->post('/instance/connect', $payload);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService connect Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * جلب رمز الـ QR Code لربط الواتساب
     * GET /instance/qr
     */
    public function getQrCode(): array
    {
        try {
            $response = $this->client()->get('/instance/qr');
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService getQrCode Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * طلب كود الاقتران عبر رقم الهاتف (بديل الـ QR Code)
     * POST /instance/pair
     */
    public function getPairingCode(string $phone): array
    {
        try {
            $response = $this->client()->post('/instance/pair', [
                'phone' => $this->formatNumber($phone),
            ]);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService getPairingCode Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * فصل الاتصال مؤقتاً
     * POST /instance/disconnect
     */
    public function disconnect(): array
    {
        try {
            $response = $this->client()->post('/instance/disconnect');
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService disconnect Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * إعادة الاتصال
     * POST /instance/reconnect
     */
    public function reconnect(): array
    {
        try {
            $response = $this->client()->post('/instance/reconnect');
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService reconnect Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * تسجيل الخروج ومسح الجلسة لربط رقم جديد
     * DELETE /instance/logout
     */
    public function logout(): array
    {
        try {
            $response = $this->client()->delete('/instance/logout');
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService logout Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * جلب معلومات وتفاصيل الإنستانس
     * GET /instance/info/{instanceId}
     */
    public function getInstanceInfo(): array
    {
        try {
            $response = $this->client()->get("/instance/info/{$this->instanceId}");
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService getInstanceInfo Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * تحديث الإعدادات المتقدمة (مثل رفض المكالمات والرد التلقائي)
     * PUT /instance/{instanceId}/advanced-settings
     */
    public function updateAdvancedSettings(array $settings): array
    {
        try {
            $response = $this->client()->put("/instance/{$this->instanceId}/advanced-settings", $settings);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('WhatsAppService updateAdvancedSettings Error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /* =========================================================================
     * 2. دوال إرسال الرسائل (Messaging Methods)
     * ========================================================================= */

    /**
     * إرسال رسالة نصية لأولياء الأمور
     * POST /send/text
     */
    public function sendText(string $phone, string $text, ?int $delay = null): array
    {
        try {
            $payload = [
                'number' => $this->formatNumber($phone),
                'text'   => $text,
            ];

            if ($delay) {
                $payload['delay'] = $delay;
            }

            $response = $this->client()->post('/send/text', $payload);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error("WhatsAppService sendText to {$phone} Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * إرسال ملف، شهادة أو تقرير PDF / صورة
     * POST /send/media
     *
     * @param string $type ('document', 'image', 'video', 'audio')
     */
    public function sendMedia(string $phone, string $fileUrl, string $type = 'document', ?string $caption = null, ?string $filename = null): array
    {
        try {
            $payload = [
                'number' => $this->formatNumber($phone),
                'type'   => $type,
                'url'    => $fileUrl,
            ];

            if ($caption) {
                $payload['caption'] = $caption;
            }

            if ($filename) {
                $payload['filename'] = $filename;
            }

            $response = $this->client()->post('/send/media', $payload);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error("WhatsAppService sendMedia to {$phone} Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * إرسال رسالة بأزرار تفاعلية (Interactive Buttons)
     * POST /send/button
     * 
     * @param array $buttons مصفوفة الأزرار: [['type' => 'reply', 'displayText' => 'نعم', 'id' => 'yes_btn']]
     */
    public function sendButton(string $phone, string $title, string $description, string $footer, array $buttons): array
    {
        try {
            $payload = [
                'number'      => $this->formatNumber($phone),
                'title'       => $title,
                'description' => $description,
                'footer'      => $footer,
                'buttons'     => $buttons,
            ];

            $response = $this->client()->post('/send/button', $payload);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error("WhatsAppService sendButton to {$phone} Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * فحص ما إذا كان الرقم مسجلاً على واتساب
     * POST /user/check
     */
    public function checkNumber(string $phone): array
    {
        try {
            $response = $this->client()->post('/user/check', [
                'number' => [$this->formatNumber($phone)],
            ]);
            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error("WhatsAppService checkNumber {$phone} Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /* =========================================================================
     * 3. دوال مساعدة (Helper Methods)
     * ========================================================================= */

    /**
     * تنظيف وتنسيق رقم الهاتف الدولي
     * إزالة الرموز مثل (+) والمسافات
     */
    protected function formatNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            $cleaned = substr($cleaned, 1);
        }

        if (!str_starts_with($cleaned, '967')) {
            $cleaned = '967' . $cleaned;
        }

        return $cleaned;
    }
}